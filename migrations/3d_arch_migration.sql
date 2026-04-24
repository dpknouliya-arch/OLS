-- ============================================================
--  3D ORDER ARCHITECTURE MIGRATION
--  Applies every schema change required for the full
--  DRAFT -> ROSTER -> SUBMIT -> FINAL ORDER flow.
--
--  Safe to re-run: all statements use IF NOT EXISTS / IF EXISTS
--  so they are idempotent on a database that is already current.
--
--  Execution order matters -- run top to bottom as a single script.
--
--  Databases touched:
--    jogjoino_online_services  (main OLS writes)
--    jogjoino_3djersey         (3D design data -- READ ONLY from OLS)
--  Date: 2026-04-24
-- ============================================================


-- ============================================================
--  STEP 1  tbl_size
--  Insert 3D jersey sizes (prod_id = 0).
--  These are the valid product_size_id values for 3D roster rows.
--  product_size_id in tbl_draft_oi / tbl_order_item must be an
--  integer FK -- never the raw text string like "AS-46".
--  INSERT IGNORE skips rows that already exist.
-- ============================================================

INSERT IGNORE INTO jogjoino_online_services.tbl_size
  (size_name, prod_id, split_order, sort_no, enable, size_of_person)
VALUES
  ('AS-46',    0, 1, 1, 1, 'adult'),
  ('AS-48',    0, 1, 2, 1, 'adult'),
  ('A4XL-50',  0, 1, 3, 1, 'adult'),
  ('A4XL-52',  0, 1, 4, 1, 'adult'),
  ('A5XL-54',  0, 1, 5, 1, 'adult'),
  ('A5XL-56',  0, 1, 6, 1, 'adult'),
  ('Youth-S',  0, 1, 7, 1, 'youth'),
  ('Youth-M',  0, 1, 8, 1, 'youth'),
  ('Youth-L',  0, 1, 9, 1, 'youth');


-- ============================================================
--  STEP 2  tbl_order_form
--  Add the columns that drive the 3D order lifecycle.
--  One row per design_order_id -- created on first roster save,
--  updated on checkout submit.
-- ============================================================

-- Link back to jogjoino_3djersey.design_order.order_id
ALTER TABLE jogjoino_online_services.tbl_order_form
  ADD COLUMN IF NOT EXISTS design_order_id INT(11) DEFAULT NULL;

-- 0 = draft (roster saved, not yet submitted)
-- 1 = submitted (checkout complete)
ALTER TABLE jogjoino_online_services.tbl_order_form
  ADD COLUMN IF NOT EXISTS is_submitted TINYINT(1) NOT NULL DEFAULT 0;

-- Timestamp written when order is first submitted via checkout
ALTER TABLE jogjoino_online_services.tbl_order_form
  ADD COLUMN IF NOT EXISTS submitted_date DATETIME DEFAULT NULL;

-- Team name and year entered on the roster step
ALTER TABLE jogjoino_online_services.tbl_order_form
  ADD COLUMN IF NOT EXISTS on_team_name VARCHAR(150) DEFAULT NULL;

ALTER TABLE jogjoino_online_services.tbl_order_form
  ADD COLUMN IF NOT EXISTS on_year VARCHAR(50) DEFAULT NULL;

-- Index for the primary lookup:  WHERE design_order_id = ?
-- (IF NOT EXISTS is only available from MySQL 8.0.21; the
--  procedure below is safe for older MySQL / MariaDB as well.)
SET @idx_exists = (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = 'jogjoino_online_services'
    AND TABLE_NAME   = 'tbl_order_form'
    AND INDEX_NAME   = 'idx_design_order_id'
);
SET @sql = IF(@idx_exists = 0,
  'ALTER TABLE jogjoino_online_services.tbl_order_form ADD INDEX idx_design_order_id (design_order_id)',
  'SELECT ''index idx_design_order_id already exists on tbl_order_form'' AS note'
);
PREPARE _s FROM @sql; EXECUTE _s; DEALLOCATE PREPARE _s;

-- UNIQUE constraint (enforce one row per 3D design order).
-- Run STEP 6 duplicate cleanup FIRST if you have existing data,
-- then uncomment the line below.
--
-- ALTER TABLE jogjoino_online_services.tbl_order_form
--   ADD UNIQUE INDEX uq_design_order_id (design_order_id);


-- ============================================================
--  STEP 3  tbl_draft_oi
--  Holds roster rows while the order is in draft state.
--  On checkout submit they are migrated to tbl_order_item and
--  deleted from here.
-- ============================================================

ALTER TABLE jogjoino_online_services.tbl_draft_oi
  ADD COLUMN IF NOT EXISTS design_order_id INT(11) DEFAULT NULL AFTER of_id;

SET @idx_exists = (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = 'jogjoino_online_services'
    AND TABLE_NAME   = 'tbl_draft_oi'
    AND INDEX_NAME   = 'idx_design_order_id'
);
SET @sql = IF(@idx_exists = 0,
  'ALTER TABLE jogjoino_online_services.tbl_draft_oi ADD INDEX idx_design_order_id (design_order_id)',
  'SELECT ''index idx_design_order_id already exists on tbl_draft_oi'' AS note'
);
PREPARE _s FROM @sql; EXECUTE _s; DEALLOCATE PREPARE _s;


-- ============================================================
--  STEP 4  tbl_order_item -- widen the sex column
--  The original column is varchar(6), which is too short for
--  the value 'female_youth' (12 chars) used by 3D orders.
--  Also widen tbl_draft_oi.sex to match (already varchar(15)
--  but included here for completeness).
-- ============================================================

ALTER TABLE jogjoino_online_services.tbl_order_item
  MODIFY COLUMN sex VARCHAR(15) DEFAULT NULL;

ALTER TABLE jogjoino_online_services.tbl_draft_oi
  MODIFY COLUMN sex VARCHAR(15) DEFAULT NULL;


-- ============================================================
--  STEP 5  (OPTIONAL) Remove OLS order-state columns that were
--  previously added to jogjoino_3djersey.design_order.
--  That database is now strictly READ-ONLY from OLS; all order
--  state lives in jogjoino_online_services.tbl_order_form.
--
--  *** Only run after the new code is fully deployed and any
--      data you need has been back-filled via STEP 8. ***
-- ============================================================

ALTER TABLE jogjoino_3djersey.design_order
  DROP COLUMN IF EXISTS is_submitted,
  DROP COLUMN IF EXISTS submitted_date,
  DROP COLUMN IF EXISTS of_id,
  DROP COLUMN IF EXISTS customer_po,
  DROP COLUMN IF EXISTS req_due_date,
  DROP COLUMN IF EXISTS sales_rep_id,
  DROP COLUMN IF EXISTS project_name,
  DROP COLUMN IF EXISTS game_event_date,
  DROP COLUMN IF EXISTS payment_opt,
  DROP COLUMN IF EXISTS reorder_num;


-- ============================================================
--  STEP 6  Duplicate-row cleanup for tbl_order_form
--  Before the one-row-per-order fix, visiting a submitted order
--  created a new draft row each time.  This step removes the
--  extras, keeping the highest of_id (most recent row) for each
--  design_order_id.
--
--  Run the SELECT first to preview, then run the DELETE.
-- ============================================================

-- Preview rows that will be removed:
--
-- SELECT t1.of_id, t1.design_order_id, t1.is_submitted, t1.order_status
-- FROM   jogjoino_online_services.tbl_order_form t1
-- WHERE  t1.design_order_id IS NOT NULL
--   AND  EXISTS (
--     SELECT 1 FROM jogjoino_online_services.tbl_order_form t2
--     WHERE  t2.design_order_id = t1.design_order_id
--       AND  t2.of_id <> t1.of_id
--   )
-- ORDER BY t1.design_order_id, t1.of_id DESC;

-- Delete duplicates -- keep the highest of_id per design_order_id:
--
-- DELETE t_old
-- FROM   jogjoino_online_services.tbl_order_form AS t_old
-- JOIN (
--   SELECT design_order_id, MAX(of_id) AS keep_id
--   FROM   jogjoino_online_services.tbl_order_form
--   WHERE  design_order_id IS NOT NULL
--   GROUP  BY design_order_id
--   HAVING COUNT(*) > 1
-- ) keeper
--   ON  t_old.design_order_id = keeper.design_order_id
--   AND t_old.of_id           <> keeper.keep_id;


-- ============================================================
--  STEP 7  (OPTIONAL) Remove old 3D-specific columns from
--  tbl_order_item added by a previous migration iteration.
--  The current flow reuses the standard columns shared with
--  the normal order flow.
--
--  *** Skip entirely if that earlier migration was never run. ***
-- ============================================================

ALTER TABLE jogjoino_online_services.tbl_order_item
  DROP COLUMN IF EXISTS pattern_cut,
  DROP COLUMN IF EXISTS player_or_goalie,
  DROP COLUMN IF EXISTS jersey_size,
  DROP COLUMN IF EXISTS jersey_no,
  DROP COLUMN IF EXISTS jersey_color,
  DROP COLUMN IF EXISTS jersey_qty,
  DROP COLUMN IF EXISTS jersey_color2,
  DROP COLUMN IF EXISTS jersey_qty2,
  DROP COLUMN IF EXISTS sock_size,
  DROP COLUMN IF EXISTS sock_color,
  DROP COLUMN IF EXISTS sock_qty,
  DROP COLUMN IF EXISTS sock_color2,
  DROP COLUMN IF EXISTS sock_qty2,
  DROP COLUMN IF EXISTS cor_a,
  DROP COLUMN IF EXISTS notes,
  DROP COLUMN IF EXISTS design_order_id;

SET @idx_exists = (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = 'jogjoino_online_services'
    AND TABLE_NAME   = 'tbl_order_item'
    AND INDEX_NAME   = 'idx_design_order_id'
);
SET @sql = IF(@idx_exists > 0,
  'DROP INDEX idx_design_order_id ON jogjoino_online_services.tbl_order_item',
  'SELECT ''index idx_design_order_id does not exist on tbl_order_item -- nothing to drop'' AS note'
);
PREPARE _s FROM @sql; EXECUTE _s; DEALLOCATE PREPARE _s;


-- ============================================================
--  STEP 8  (OPTIONAL) Historical data back-fill
--  Copies roster rows from jogjoino_3djersey.order_team into
--  tbl_order_item for orders that were placed via the old flow
--  but never migrated.
--  Requires tbl_order_form rows with correct design_order_id.
--
--  Run the SELECT preview first; if it returns rows, run the INSERT.
-- ============================================================

-- Preview -- orders that have no tbl_order_item rows yet:
--
-- SELECT ot.order_id, tof.of_id, ot.player_name, ot.jersey_size
-- FROM   jogjoino_3djersey.order_team ot
-- JOIN   jogjoino_online_services.tbl_order_form tof
--          ON tof.design_order_id = ot.order_id
-- LEFT JOIN jogjoino_online_services.tbl_order_item oi
--          ON oi.of_id = tof.of_id
-- WHERE  oi.of_id IS NULL;

-- Back-fill INSERT (maps order_team columns to tbl_order_item columns;
-- resolves jersey_size text to integer product_size_id via tbl_size):
--
-- INSERT INTO jogjoino_online_services.tbl_order_item
--   (of_id, player_name, sex, p_or_g,
--    product_size_id, jersey_number,
--    color_top1, qty_top1, color_top2, qty_top2,
--    bottom_size, color_bottom1, qty_bottom1, color_bottom2, qty_bottom2,
--    c_or_a, name_for_packing, note)
-- SELECT
--   tof.of_id,
--   ot.player_name,
--   ot.pattern_cut,                         -- maps to sex
--   COALESCE(ot.player_or_goalie, 'player'), -- maps to p_or_g
--   COALESCE(sz.size_id, 0),                 -- text -> integer FK
--   ot.jersey_no,
--   ot.jersey_color,   ot.jersey_qty,
--   ot.jersey_color2,  ot.jersey_qty2,
--   ot.sock_size,
--   ot.sock_color,     ot.sock_qty,
--   ot.sock_color2,    ot.sock_qty2,
--   ot.cor_a,
--   ot.name_for_packing,
--   ot.notes
-- FROM jogjoino_3djersey.order_team ot
-- JOIN jogjoino_online_services.tbl_order_form tof
--        ON tof.design_order_id = ot.order_id
-- LEFT JOIN jogjoino_online_services.tbl_size sz
--        ON LOWER(TRIM(sz.size_name)) = LOWER(TRIM(ot.jersey_size))
-- WHERE NOT EXISTS (
--   SELECT 1 FROM jogjoino_online_services.tbl_order_item oi
--   WHERE  oi.of_id = tof.of_id
-- );


-- ============================================================
--  END OF MIGRATION
-- ============================================================
