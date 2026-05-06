<?php
include('check-session.php');
// 
include 'encryption_helper.php';
$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id = $obj_user->user_id;

?>

<style>
    .updated_dateTxt {
        color: #FFF;
        background: green;
        padding: 2px;
        border-radius: 26px;
        padding: 2px 5px;
        font-size: 13px !important;
        text-align: center;
    }
</style>

<style>
    /* ── ROOT TOKENS ──────────────────────────────────────────────── */
    :root {
      --jog-dark:       #0a0e1a;
      --jog-sidebar:    #111827;
      --jog-sidebar-hover: #1c2537;
      --jog-blue:       #1d4ed8;
      --jog-blue-light: #3b82f6;
      --jog-accent:     #e8b830;
      --jog-border:     rgba(255,255,255,0.07);
      --body-bg:        #f0f2f7;
      --card-bg:        #ffffff;
      --text-main:      #111827;
      --text-muted:     #6b7280;
      --text-light:     #9ca3af;
      --radius-card:    12px;
      --radius-badge:   6px;
      --shadow-card:    0 2px 12px rgba(0,0,0,0.08);
      --shadow-hover:   0 8px 32px rgba(0,0,0,0.14);
      --font-head:      'Barlow Condensed', sans-serif;
      --font-body:      'DM Sans', sans-serif;
      --sidebar-w:      230px;
      --topbar-h:       62px;
    }

    /* ── RESET / BASE ─────────────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; }
    body {
      font-family: var(--font-body);
      background: var(--body-bg);
      color: var(--text-main);
      min-height: 100vh;
      margin: 0;
    }

    /* ── SIDEBAR ──────────────────────────────────────────────────── */
    #sidebar {
      width: var(--sidebar-w);
      min-height: 100vh;
      background: var(--jog-sidebar);
      position: fixed;
      top: 0; left: 0;
      z-index: 200;
      display: flex;
      flex-direction: column;
      transition: transform .28s cubic-bezier(.4,0,.2,1);
    }
    .sidebar-logo {
      padding: 20px 20px 16px;
      border-bottom: 1px solid var(--jog-border);
      display: flex; align-items: center; gap: 10px;
    }
    .sidebar-logo .logo-mark {
      width: 34px; height: 34px;
      background: var(--jog-blue);
      border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
    }
    .sidebar-logo .logo-mark i { color: #fff; font-size: 16px; }
    .sidebar-logo .logo-text {
      font-family: var(--font-head);
      font-size: 20px; font-weight: 800;
      letter-spacing: .5px; color: #fff;
      line-height: 1;
    }
    .sidebar-logo .logo-sub {
      font-size: 9px; font-weight: 500;
      letter-spacing: 1.4px; text-transform: uppercase;
      color: rgba(255,255,255,.35); margin-top: 2px;
    }
    .sidebar-nav { flex: 1; padding: 10px 0; overflow-y: auto; }
    .nav-section-label {
      font-size: 9.5px; font-weight: 600;
      letter-spacing: 1.8px; text-transform: uppercase;
      color: rgba(255,255,255,.28);
      padding: 14px 20px 6px;
    }
    .sidebar-link {
      display: flex; align-items: center; gap: 11px;
      padding: 9px 20px;
      color: rgba(255,255,255,.58);
      font-size: 13.5px; font-weight: 400;
      text-decoration: none;
      border-left: 3px solid transparent;
      transition: all .18s ease;
    }
    .sidebar-link i { font-size: 15px; width: 18px; flex-shrink: 0; }
    .sidebar-link:hover {
      background: var(--jog-sidebar-hover);
      color: rgba(255,255,255,.9);
    }
    .sidebar-link.active {
      background: rgba(29,78,216,.18);
      color: #fff;
      border-left-color: var(--jog-blue-light);
      font-weight: 500;
    }
    .sidebar-footer {
      padding: 14px 20px;
      border-top: 1px solid var(--jog-border);
    }
    .sidebar-footer .user-row {
      display: flex; align-items: center; gap: 10px;
    }
    .avatar-sm {
      width: 32px; height: 32px;
      border-radius: 50%;
      background: var(--jog-blue);
      display: flex; align-items: center; justify-content: center;
      font-family: var(--font-head);
      font-size: 13px; font-weight: 700; color: #fff;
      flex-shrink: 0;
    }
    .user-name { font-size: 13px; font-weight: 500; color: rgba(255,255,255,.75); }
    .user-role { font-size: 11px; color: rgba(255,255,255,.35); }

    /* ── TOP BAR ──────────────────────────────────────────────────── */
    #topbar {
      position: fixed; top: 0;
      left: var(--sidebar-w); right: 0;
      height: var(--topbar-h);
      background: #fff;
      border-bottom: 1px solid #e5e7eb;
      display: flex; align-items: center;
      padding: 0 24px; gap: 16px;
      z-index: 100;
    }
    .topbar-title {
      font-family: var(--font-head);
      font-size: 20px; font-weight: 700;
      letter-spacing: .3px; color: var(--text-main);
      flex: 1;
    }
    .topbar-tabs {
      display: flex; gap: 2px;
      background: #f3f4f6;
      border-radius: 8px;
      padding: 4px;
    }
    .tab-btn {
      padding: 6px 18px;
      border-radius: 6px;
      border: none;
      background: transparent;
      font-family: var(--font-body);
      font-size: 13px; font-weight: 500;
      color: var(--text-muted);
      cursor: pointer;
      transition: all .18s ease;
    }
    .tab-btn.active {
      background: #fff;
      color: var(--jog-blue);
      box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    }
    .topbar-right { display: flex; align-items: center; gap: 10px; }
    .topbar-greeting { font-size: 13px; font-weight: 500; color: var(--text-muted); }
    .topbar-greeting span { color: var(--text-main); font-weight: 600; }
    .topbar-icon-btn {
      width: 36px; height: 36px;
      border-radius: 8px; border: 1px solid #e5e7eb;
      background: transparent;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; color: var(--text-muted);
      transition: all .15s;
    }
    .topbar-icon-btn:hover { background: #f3f4f6; color: var(--text-main); }

    /* ── MAIN CONTENT ─────────────────────────────────────────────── */
    #main { 
      padding: 28px 28px 60px;
      min-height: calc(100vh - var(--topbar-h));
      transition: margin-left .28s cubic-bezier(.4,0,.2,1);
    }

    /* ── TOOLBAR (search / filter / sort) ────────────────────────── */
    .toolbar-card {
      background: #fff;
      border-radius: var(--radius-card);
      border: 1px solid #e5e7eb;
      padding: 14px 18px;
      margin-bottom: 22px;
      display: flex; align-items: center; gap: 12px;
      flex-wrap: wrap;
    }
    .toolbar-search {
      flex: 1; min-width: 200px;
      position: relative;
    }
    .toolbar-search i {
      position: absolute; left: 12px; top: 50%;
      transform: translateY(-50%);
      color: var(--text-light); font-size: 14px;
      pointer-events: none;
    }
    .toolbar-search input {
      width: 100%;
      padding: 8px 14px 8px 36px;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      font-family: var(--font-body);
      font-size: 13.5px;
      color: var(--text-main);
      background: #f9fafb;
      outline: none;
      transition: border-color .15s;
    }
    .toolbar-search input:focus { border-color: var(--jog-blue-light); background: #fff; }
    .toolbar-select {
      padding: 8px 32px 8px 12px;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      font-family: var(--font-body);
      font-size: 13px; color: var(--text-main);
      background: #f9fafb url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat right 10px center;
      appearance: none; -webkit-appearance: none;
      cursor: pointer; outline: none;
      transition: border-color .15s;
    }
    .toolbar-select:focus { border-color: var(--jog-blue-light); background-color: #fff; }
    .toolbar-count {
      margin-left: auto;
      font-size: 13px; color: var(--text-muted);
      white-space: nowrap;
    }
    .btn-new {
      padding: 8px 16px;
      background: var(--jog-blue);
      color: #fff;
      border: none; border-radius: 8px;
      font-family: var(--font-body);
      font-size: 13px; font-weight: 500;
      cursor: pointer;
      display: flex; align-items: center; gap: 6px;
      transition: background .15s;
      text-decoration: none;
      white-space: nowrap;
    }
    .btn-new:hover { background: #1a44b8; color: #fff; }

    /* ── PAGE SECTIONS ────────────────────────────────────────────── */
    .page-section { display: none; }
    .page-section.active { display: block; }

    /* ── DRAFT CARDS ──────────────────────────────────────────────── */
    .draft-card {
      background: var(--card-bg);
      border-radius: var(--radius-card);
      border: 1px solid #e5e7eb;
      box-shadow: var(--shadow-card);
      overflow: hidden;
      transition: box-shadow .2s ease, transform .2s ease;
      position: relative;
      display: flex; flex-direction: column;
    }
    .draft-card:hover {
      box-shadow: var(--shadow-hover);
      transform: translateY(-2px);
    }
    .draft-card .card-thumb {
      background: linear-gradient(145deg, #eef2ff 0%, #f0f4ff 100%);
      height: 160px;
      display: flex; align-items: center; justify-content: center;
      position: relative;
      overflow: hidden;
    }
    .draft-card .card-thumb .jersey-img {
      height: 130px;
      object-fit: contain;
      filter: drop-shadow(0 4px 12px rgba(0,0,0,0.15));
      transition: transform .25s ease;
    }
    .draft-card:hover .card-thumb .jersey-img { transform: scale(1.06) translateY(-3px); }
    .draft-card .card-thumb .style-badge {
      position: absolute; top: 10px; left: 10px;
      background: rgba(255,255,255,.9);
      backdrop-filter: blur(6px);
      border: 1px solid rgba(0,0,0,.06);
      padding: 3px 9px;
      border-radius: 20px;
      font-size: 11px; font-weight: 600;
      color: var(--jog-blue);
    }
    .draft-card .card-thumb .thumb-actions {
      position: absolute; top: 10px; right: 10px;
    }
    .draft-card .card-body-inner {
      padding: 14px 16px;
      flex: 1; display: flex; flex-direction: column; gap: 10px;
    }
    .draft-card .card-title-row {
      display: flex; align-items: flex-start; justify-content: space-between; gap: 8px;
    }
    .draft-card .card-team-name {
      font-family: var(--font-head);
      font-size: 16px; font-weight: 700;
      letter-spacing: .2px; color: var(--text-main);
      line-height: 1.2;
    }
    .draft-card .rep-avatar {
      height: 28px;
      border-radius: 15px;
      padding: 15px;
      background: var(--jog-blue);
      display: flex; align-items: center; justify-content: center;
      font-family: var(--font-head);
      font-size: 11px; font-weight: 700; color: #fff;
      flex-shrink: 0;
    }
    .draft-card .meta-row {
      display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap;
    }
    .meta-item {
      display: flex; align-items: center; gap: 5px;
      font-size: 12px; color: var(--text-muted);
    }
    .meta-item i { font-size: 12px; color: var(--text-light); }
    .draft-card .roster-progress { margin-top: 2px; }
    .roster-label {
      display: flex; justify-content: space-between; align-items: center;
      font-size: 11.5px; color: var(--text-muted); margin-bottom: 5px;
    }
    .progress-bar-track {
      height: 5px; border-radius: 10px;
      background: #e5e7eb; overflow: hidden;
    }
    .progress-bar-fill {
      height: 100%; border-radius: 10px;
      background: linear-gradient(90deg, var(--jog-blue), var(--jog-blue-light));
      transition: width .4s ease;
    }
    .progress-bar-fill.complete { background: linear-gradient(90deg, #059669, #34d399); }

    /* ── STATUS BADGES ────────────────────────────────────────────── */
    .status-badge {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 3px 9px;
      border-radius: var(--radius-badge);
      font-size: 11px; font-weight: 600;
      letter-spacing: .3px;
      text-transform: uppercase;
    }
    .status-badge::before {
      content: ''; width: 6px; height: 6px;
      border-radius: 50%; flex-shrink: 0;
    }
    /* Draft statuses */
    .sb-draft         { background: #f3f4f6; color: #005aff; }
    .sb-draft::before { background: #005dfd; }
    .sb-design-complete         { background: #eff6ff; color: #1d4ed8; }
    .sb-design-complete::before { background: #3b82f6; }
    .sb-roster-progress         { background: #fffbeb; color: #b45309; }
    .sb-roster-progress::before { background: #f59e0b; }
    .sb-ready         { background: #f0fdf4; color: #15803d; }
    .sb-ready::before { background: #22c55e; }
    /* Order statuses */
    .sb-submitted           { background: #eff6ff; color: #1d4ed8; }
    .sb-submitted::before   { background: #3b82f6; }
    .sb-under-review        { background: #fff7ed; color: #c2410c; }
    .sb-under-review::before{ background: #f97316; }
    .sb-approved            { background: #f0fdf4; color: #15803d; }
    .sb-approved::before    { background: #22c55e; }
    .sb-in-production       { background: #f5f3ff; color: #7c3aed; }
    .sb-in-production::before{ background: #8b5cf6; }
    .sb-qc-complete         { background: #f0fdfa; color: #0f766e; }
    .sb-qc-complete::before { background: #14b8a6; }
    .sb-shipped             { background: #eff6ff; color: #1e40af; }
    .sb-shipped::before     { background: #1d4ed8; }
    .sb-delivered           { background: #f9fafb; color: #374151; }
    .sb-delivered::before   { background: #6b7280; }

    /* ── DRAFT CARD FOOTER ────────────────────────────────────────── */
    .draft-card .card-footer-inner {
      padding: 10px 16px;
      border-top: 1px solid #f3f4f6;
      display: flex; align-items: center; gap: 8px;
    }
    .btn-primary-cta {
      flex: 1;
      padding: 8px 14px;
      background: var(--jog-blue);
      color: #fff; border: none; border-radius: 7px;
      font-family: var(--font-body);
      font-size: 13px; font-weight: 500;
      cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 6px;
      transition: background .15s;
    }
    .btn-primary-cta:hover { background: #1a44b8; }
    .btn-primary-cta.btn-roster { background: #059669; }
    .btn-primary-cta.btn-roster:hover { background: #047857; }
    .btn-dots {
      width: 34px; height: 34px;
      border-radius: 7px; border: 1px solid #e5e7eb;
      background: #fff;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; color: var(--text-muted);
      transition: all .15s;
      flex-shrink: 0;
    }
    .btn-dots:hover { background: #f3f4f6; color: var(--text-main); }

    /* ── ORDER CARD ───────────────────────────────────────────────── */
    .order-card {
      background: var(--card-bg);
      border-radius: var(--radius-card);
      border: 1px solid #e5e7eb;
      box-shadow: var(--shadow-card);
      overflow: hidden;
      cursor: pointer;
      transition: box-shadow .2s ease, transform .2s ease;
      display: flex; flex-direction: column;
    }
    .order-card:hover {
      box-shadow: var(--shadow-hover);
      transform: translateY(-2px);
    }
    /* ── ORDER CARD THUMBNAIL ────────────────────────────────────── */
    .order-card .card-thumb {
      height: 172px;
      position: relative;
      overflow: hidden;
      border-radius: 0;
    }
    .order-card .card-thumb .jersey-wrap {
      width: 100%; height: 100%;
      display: flex; align-items: center; justify-content: center;
      padding: 16px 20px 10px;
    }
    .order-card .card-thumb .jersey-wrap img {
      height: 130px;
      object-fit: contain;
      filter: drop-shadow(0 6px 14px rgba(0,0,0,.22));
      transition: transform .28s ease;
    }
    .order-card:hover .card-thumb .jersey-wrap img {
      transform: scale(1.07) translateY(-4px);
    }
    .order-card .card-thumb .oc-order-id {
      position: absolute; top: 10px; right: 10px;
      background: rgba(0,0,0,.45);
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
      color: rgba(255,255,255,.9);
      font-family: 'Courier New', monospace;
      font-size: 11px; font-weight: 700;
      letter-spacing: .4px;
      padding: 3px 8px;
      border-radius: 5px;
    }
    .order-card .card-thumb .oc-status-badge {
      position: absolute; bottom: 10px; left: 10px;
    }
    .order-card .order-id {
      font-family: var(--font-head);
      font-size: 13px; font-weight: 700;
      color: var(--text-muted);
      letter-spacing: .4px;
    }
    .order-card .order-name {
      font-family: var(--font-head);
      font-size: 17px; font-weight: 700;
      color: var(--text-main); line-height: 1.2;
      margin-top: 2px;
    }
    .order-card .qty-chip {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 3px 9px;
      background: #f3f4f6;
      border-radius: 20px;
      font-size: 12px; font-weight: 500;
      color: var(--text-muted);
    }

    /* ── ORDER DETAIL PANEL ───────────────────────────────────────── */
    #order-detail-overlay {
      position: fixed; inset: 0;
      background: rgba(0,0,0,.45);
      z-index: 400;
      opacity: 0; pointer-events: none;
      transition: opacity .25s ease;
    }
    #order-detail-overlay.open { opacity: 1; pointer-events: all; }
    #order-detail-panel {
      position: fixed;
      top: 0; right: 0; bottom: 0;
      width: min(680px, 100vw);
      background: #fff;
      z-index: 500;
      display: flex; flex-direction: column;
      transform: translateX(100%);
      transition: transform .3s cubic-bezier(.4,0,.2,1);
      box-shadow: -8px 0 40px rgba(0,0,0,.15);
    }
    #order-detail-panel.open { transform: translateX(0); }
    .panel-header {
      padding: 20px 24px;
      border-bottom: 1px solid #e5e7eb;
      display: flex; align-items: center; gap: 14px;
      flex-shrink: 0;
    }
    .panel-header .panel-order-id {
      font-size: 12px; font-weight: 600;
      letter-spacing: .6px; color: var(--text-muted);
      text-transform: uppercase;
    }
    .panel-header .panel-title {
      font-family: var(--font-head);
      font-size: 22px; font-weight: 800;
      color: var(--text-main); line-height: 1;
    }
    .panel-close {
      margin-left: auto;
      width: 36px; height: 36px;
      border-radius: 8px; border: 1px solid #e5e7eb;
      background: transparent;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; color: var(--text-muted);
      font-size: 18px;
      transition: all .15s;
      flex-shrink: 0;
    }
    .panel-close:hover { background: #fee2e2; color: #dc2626; border-color: #fecaca; }
    .panel-body {
      flex: 1; overflow-y: auto; padding: 24px;
    }
    .panel-section-title {
      font-family: var(--font-head);
      font-size: 14px; font-weight: 700;
      letter-spacing: .6px; text-transform: uppercase;
      color: var(--text-muted);
      margin-bottom: 14px;
      padding-bottom: 8px;
      border-bottom: 1px solid #f3f4f6;
    }

    /* ── STATUS TIMELINE ──────────────────────────────────────────── */
    .status-timeline {
      display: flex; align-items: flex-start;
      gap: 0;
      overflow-x: auto;
      padding: 4px 0 16px;
    }
    .timeline-step {
      display: flex; flex-direction: column; align-items: center;
      flex: 1; min-width: 70px; position: relative;
    }
    .timeline-step:not(:last-child)::after {
      content: '';
      position: absolute;
      top: 16px; left: calc(50% + 16px);
      width: calc(100% - 32px);
      height: 2px;
      background: #e5e7eb;
      z-index: 0;
    }
    .timeline-step.done:not(:last-child)::after { background: var(--jog-blue); }
    .timeline-dot {
      width: 32px; height: 32px;
      border-radius: 50%;
      border: 2px solid #e5e7eb;
      background: #fff;
      display: flex; align-items: center; justify-content: center;
      font-size: 13px; color: #9ca3af;
      z-index: 1; position: relative;
      transition: all .2s;
      flex-shrink: 0;
    }
    .timeline-step.done .timeline-dot {
      background: var(--jog-blue);
      border-color: var(--jog-blue);
      color: #fff;
    }
    .timeline-step.current .timeline-dot {
      border-color: var(--jog-blue);
      color: var(--jog-blue);
      box-shadow: 0 0 0 4px rgba(29,78,216,.12);
    }
    .timeline-label {
      font-size: 10.5px; font-weight: 500;
      color: var(--text-light);
      text-align: center; margin-top: 7px;
      line-height: 1.3;
    }
    .timeline-step.done .timeline-label,
    .timeline-step.current .timeline-label { color: var(--jog-blue); font-weight: 600; }

    /* ── JERSEY PREVIEW GRID ──────────────────────────────────────── */
    .jersey-preview-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 8px;
      margin-bottom: 20px;
    }
    .jersey-preview-item {
      background: #f0f4ff;
      border-radius: 8px;
      border: 1px solid #e0e7ff;
      padding: 10px 8px 8px;
      display: flex; flex-direction: column; align-items: center;
      gap: 6px;
    }
    .jersey-preview-item img {
      height: 80px; object-fit: contain;
      filter: drop-shadow(0 2px 6px rgba(0,0,0,.12));
    }
    .jersey-preview-item .view-label {
      font-size: 10px; font-weight: 600;
      text-transform: uppercase; letter-spacing: .5px;
      color: var(--text-muted);
    }

    /* ── ROSTER TABLE ─────────────────────────────────────────────── */
    .roster-table-wrap { border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb; }
    .roster-table {
      width: 100%; border-collapse: collapse;
      font-size: 13px;
    }
    .roster-table th {
      background: var(--jog-dark);
      color: rgba(255,255,255,.8);
      font-size: 11px; font-weight: 600;
      letter-spacing: .6px; text-transform: uppercase;
      padding: 10px 12px; text-align: left;
    }
    .roster-table td {
      padding: 9px 12px;
      border-bottom: 1px solid #f3f4f6;
      color: var(--text-main);
      font-size: 13px;
    }
    .roster-table tr:last-child td { border-bottom: none; }
    .roster-table tr:nth-child(even) td { background: #f9fafb; }
    .flag-chip {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 2px 7px; border-radius: 4px;
      background: #f3f4f6; font-size: 12px;
    }
    .captain-badge {
      display: inline-flex; align-items: center; justify-content: center;
      width: 20px; height: 20px; border-radius: 4px;
      font-size: 11px; font-weight: 700; color: #fff;
      background: var(--jog-blue);
    }

    /* ── PDF DOWNLOAD SECTION ─────────────────────────────────────── */
    .pdf-download-card {
      background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
      border-radius: 10px; padding: 16px 18px;
      display: flex; align-items: center; gap: 14px;
      margin-bottom: 20px;
    }
    .pdf-icon-wrap {
      width: 44px; height: 44px;
      background: rgba(255,255,255,.15);
      border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      font-size: 22px; color: #fff;
      flex-shrink: 0;
    }
    .pdf-download-card .pdf-info { flex: 1; }
    .pdf-download-card .pdf-title { font-size: 14px; font-weight: 600; color: #fff; }
    .pdf-download-card .pdf-sub { font-size: 12px; color: rgba(255,255,255,.65); margin-top: 2px; }
    .btn-pdf-download {
      padding: 8px 16px;
      background: rgba(255,255,255,.15);
      border: 1px solid rgba(255,255,255,.25);
      border-radius: 7px; color: #fff;
      font-family: var(--font-body);
      font-size: 13px; font-weight: 500;
      cursor: pointer; display: flex; align-items: center; gap: 6px;
      transition: background .15s;
      white-space: nowrap;
    }
    .btn-pdf-download:hover { background: rgba(255,255,255,.25); }

    /* ── TRACKING SECTION ─────────────────────────────────────────── */
    .tracking-card {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      border-radius: 10px;
      padding: 14px 16px;
      display: flex; align-items: center; gap: 12px;
    }
    .tracking-card i { font-size: 24px; color: #059669; }
    .tracking-info { flex: 1; }
    .tracking-info .track-label { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: #059669; }
    .tracking-info .track-num { font-size: 15px; font-weight: 700; font-family: 'Courier New', monospace; color: var(--text-main); }
    .tracking-info .track-carrier { font-size: 12px; color: var(--text-muted); }
    .btn-track {
      padding: 8px 14px;
      background: #059669; color: #fff;
      border: none; border-radius: 7px;
      font-family: var(--font-body);
      font-size: 13px; font-weight: 500;
      cursor: pointer; display: flex; align-items: center; gap: 6px;
      transition: background .15s; white-space: nowrap;
    }
    .btn-track:hover { background: #047857; }

    /* ── PANEL FOOTER ACTIONS ─────────────────────────────────────── */
    .panel-footer {
      padding: 16px 24px;
      border-top: 1px solid #e5e7eb;
      display: flex; gap: 10px;
      flex-shrink: 0;
      flex-wrap: wrap;
    }
    .btn-panel-action {
      flex: 1; min-width: 120px;
      padding: 10px 14px;
      border-radius: 8px;
      font-family: var(--font-body);
      font-size: 13px; font-weight: 500;
      cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 6px;
      transition: all .15s;
      border: 1px solid #e5e7eb;
      background: #f9fafb; color: var(--text-main);
    }
    .btn-panel-action:hover { background: #f3f4f6; }
    .btn-panel-action.primary { background: var(--jog-blue); border-color: var(--jog-blue); color: #fff; }
    .btn-panel-action.primary:hover { background: #1a44b8; }
    .btn-panel-action.success { background: #059669; border-color: #059669; color: #fff; }
    .btn-panel-action.success:hover { background: #047857; }

    /* ── EMPTY STATE ──────────────────────────────────────────────── */
    .empty-state {
      text-align: center; padding: 60px 20px;
      color: var(--text-muted);
    }
    .empty-state i { font-size: 48px; color: #d1d5db; margin-bottom: 16px; }
    .empty-state h4 { font-family: var(--font-head); font-size: 20px; font-weight: 700; color: #374151; }
    .empty-state p { font-size: 14px; max-width: 320px; margin: 0 auto; }

    /* ── DROPDOWN MENUS ───────────────────────────────────────────── */
    .custom-dropdown { position: relative; }
    .custom-dropdown-menu {
      position: absolute; right: 0; top: calc(100% + 6px);
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      box-shadow: 0 8px 24px rgba(0,0,0,.12);
      min-width: 190px;
      z-index: 300;
      display: none;
      overflow: hidden;
    }
    .custom-dropdown-menu.open { display: block; }
    .dropdown-item-custom {
      display: flex; align-items: center; gap: 10px;
      padding: 10px 14px;
      font-size: 13.5px; font-weight: 400;
      color: var(--text-main);
      cursor: pointer;
      transition: background .12s;
      border: none; background: none; width: 100%; text-align: left;
      text-decoration: none;
    }
    .dropdown-item-custom i { font-size: 14px; color: var(--text-muted); width: 16px; }
    .dropdown-item-custom:hover { background: #f3f4f6; }
    .dropdown-item-custom.danger { color: #dc2626; }
    .dropdown-item-custom.danger i { color: #dc2626; }
    .dropdown-item-custom.danger:hover { background: #fef2f2; }
    .dropdown-divider { height: 1px; background: #f3f4f6; margin: 4px 0; }

    /* ── RESPONSIVE ───────────────────────────────────────────────── */
    @media (max-width: 991px) {
      #sidebar { transform: translateX(-100%); }
      #sidebar.open { transform: translateX(0); }
      #main { margin-left: 0; }
      #topbar { left: 0; }
      .jersey-preview-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 575px) {
      #main { padding: 16px 14px 40px; }
      .toolbar-card { padding: 10px 12px; }
      .jersey-preview-grid { grid-template-columns: repeat(2, 1fr); }
      #order-detail-panel { width: 100vw; }
    }

    /* ── TOAST NOTIFICATION ───────────────────────────────────────── */
    .toast-wrap {
      position: fixed; bottom: 24px; right: 24px;
      z-index: 9999; display: flex; flex-direction: column; gap: 8px;
    }
    .toast-msg {
      background: #111827; color: #fff;
      padding: 12px 18px; border-radius: 8px;
      font-size: 13.5px; font-weight: 500;
      box-shadow: 0 4px 16px rgba(0,0,0,.2);
      display: flex; align-items: center; gap: 10px;
      animation: toastIn .25s ease;
    }
    .toast-msg i { color: #34d399; font-size: 16px; }
    @keyframes toastIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* ── SCROLLBAR ────────────────────────────────────────────────── */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    .deleteDraftsItems img{     width: 13px;   height: 13px;  object-fit: contain; }
     .deleteDraftsItems .dropdown-item-custom{ padding: 3px 6px; border: 1px solid #f3b1b175;  border-radius: 4px;}
  </style>

<!-- ═══════════════════════════════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════════════════════════════ -->
<main id="main">

    <!-- ─── 3D DRAFT PAGE ─────────────────────────────────── -->
    <section id="section-draft" class="page-section active">

        <!-- Toolbar -->
        <div class="toolbar-card">
            <div class="toolbar-search">
                <i class="bi bi-search"></i>
                <input type="text" id="draft-search" placeholder="Search team name, style, rep…" oninput="filterDrafts()">
            </div>
            <select class="toolbar-select" id="draft-filter-status" onchange="filterDrafts()">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="design-complete">Design Complete</option>
                <option value="roster-progress">Roster In Progress</option>
                <option value="ready">Ready to Submit</option>
            </select>
            <select class="toolbar-select" id="draft-sort" onchange="filterDrafts()">
                <option value="modified">Last Modified</option>
                <option value="created">Created Date</option>
                <option value="name">Team Name A–Z</option>
            </select>
            <span class="toolbar-count" id="draft-count">6 drafts</span>
            <!-- <a href="#" class="btn-new"><i class="bi bi-plus-lg"></i>New Design</a> -->
        </div>

        <!-- Card Grid -->
        <div class="row g-3" id="draft-grid">
            <!-- Cards injected by JS -->
        </div>        
    </section>


    <!-- ─── 3D ORDERS PAGE ────────────────────────────────── -->
    <section id="section-orders" class="page-section">

        <!-- Toolbar -->
        <div class="toolbar-card">
            <div class="toolbar-search">
                <i class="bi bi-search"></i>
                <input type="text" id="orders-search" placeholder="Search Order ID, style, team…" oninput="filterOrders()">
            </div>
            <select class="toolbar-select" id="orders-filter-status" onchange="filterOrders()">
                <option value="">All Statuses</option>
                <option value="submitted">Submitted</option>
                <option value="under-review">Under Review</option>
                <option value="approved">Approved</option>
                <option value="in-production">In Production</option>
                <option value="qc-complete">QC Complete</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
            </select>
            <select class="toolbar-select">
                <option>Date Submitted ↓</option>
                <option>Date Submitted ↑</option>
                <option>Order ID</option>
            </select>
            <span class="toolbar-count" id="orders-count">8 orders</span>
        </div>

        <!-- Card Grid -->
        <div class="row g-3" id="orders-grid">
            <!-- Cards injected by JS -->
        </div>
    </section>

</main>


<!-- ═══════════════════════════════════════════════════════════
     ORDER DETAIL PANEL
═══════════════════════════════════════════════════════════ -->
<div id="order-detail-overlay" onclick="closeOrderPanel()"></div>

<div id="order-detail-panel">
    <div class="panel-header">
        <div>
            <div class="panel-order-id" id="panel-order-id">—</div>
            <div class="panel-title" id="panel-order-name">—</div>
        </div>
        <div id="panel-status-badge" style="margin-left:12px;"></div>
        <button class="panel-close" onclick="closeOrderPanel()"><i class="bi bi-x-lg"></i></button>
    </div>

    <div class="panel-body" id="panel-body">
        <!-- injected by openOrderPanel() -->
    </div>

    <div class="panel-footer">
        <button class="btn-panel-action primary" data-toast="Opening PDF…" onclick="handleToast(this)">
            <i class="bi bi-file-pdf"></i> View PDF
        </button>
        <button class="btn-panel-action" data-toast="Roster view loaded" onclick="handleToast(this)">
            <i class="bi bi-list-ul"></i> View Roster
        </button>
        <button class="btn-panel-action success" data-toast="Reorder draft created!" onclick="handleToast(this)">
            <i class="bi bi-arrow-repeat"></i> Reorder
        </button>
        <button class="btn-panel-action" data-toast="Invoice downloading…" onclick="handleToast(this)">
            <i class="bi bi-receipt"></i> Invoice
        </button>
    </div>
</div>


<!-- ═══════════════════════════════════════════════════════════
     TOAST CONTAINER
═══════════════════════════════════════════════════════════ -->


<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
const D_BASE_URL = "<?= D_BASE_URL ?>";

/* ═══════════════════════════════════════════════════════════
   DUMMY DATA
═══════════════════════════════════════════════════════════ */

// Jersey placeholder — realistic hockey jersey silhouette
function jerseyPlaceholder(body, yoke, stripe1, stripe2) {
  body   = body   || '#1d4ed8';
  yoke   = yoke   || '#0a0e1a';
  stripe1= stripe1|| '#111827';
  stripe2= stripe2|| '#c9cacc';
  const svg = [
    "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 160 180'>",
    // left sleeve
    "<path d='M10,54 C8,46 6,40 8,33 L26,23 C27,30 29,38 31,54 Z' fill='" + body + "'/>",
    "<path d='M10,54 C8,46 6,40 8,33 L17,28 C18,33 19,39 21,54 Z' fill='" + yoke + "'/>",
    // right sleeve
    "<path d='M150,54 C152,46 154,40 152,33 L134,23 C133,30 131,38 129,54 Z' fill='" + body + "'/>",
    "<path d='M150,54 C152,46 154,40 152,33 L143,28 C142,33 141,39 139,54 Z' fill='" + yoke + "'/>",
    // body
    "<path d='M31,32 C42,18 56,13 80,12 C104,13 118,18 129,32 L134,162 L26,162 Z' fill='" + body + "'/>",
    // yoke
    "<path d='M53,17 Q66,11 80,12 Q94,11 107,17 L112,32 C100,27 90,24 80,25 C70,24 60,27 48,32 Z' fill='" + yoke + "'/>",
    // collar
    "<path d='M69,14 Q80,9 91,14 L89,21 Q80,17 71,21 Z' fill='" + yoke + "'/>",
    "<ellipse cx='80' cy='15' rx='13' ry='6' fill='" + yoke + "'/>",
    // sleeve stripes
    "<rect x='9' y='57' width='22' height='5' rx='1' fill='" + stripe1 + "'/>",
    "<rect x='9' y='63' width='22' height='4' rx='1' fill='" + stripe2 + "'/>",
    "<rect x='129' y='57' width='22' height='5' rx='1' fill='" + stripe1 + "'/>",
    "<rect x='129' y='63' width='22' height='4' rx='1' fill='" + stripe2 + "'/>",
    // body stripes
    "<rect x='26' y='130' width='108' height='7' rx='2' fill='" + stripe1 + "'/>",
    "<rect x='26' y='138' width='108' height='5' rx='2' fill='" + stripe2 + "'/>",
    "</svg>"
  ].join('');
  return 'data:image/svg+xml,' + encodeURIComponent(svg);
}



// const DRAFTS = [
//   { id: 'DFT-2026-0421', name: '#2384-2', style: 'Tampa Bay-2', created: 'Mar 9, 2026', modified: 'Mar 11, 2026', rep: 'RV', rosterDone: 12, rosterTotal: 12, status: 'ready', color: '#1d4ed8', yoke: '#0a0e1a' },
//   { id: 'DFT-2026-0418', name: 'Home / Away', style: 'Tampa Bay-2', created: 'Mar 7, 2026', modified: 'Mar 10, 2026', rep: 'JM', rosterDone: 8, rosterTotal: 15, status: 'roster-progress', color: '#dc2626', yoke: '#111827' },
//   { id: 'DFT-2026-0415', name: 'Tampa Bay-2', style: 'Big Hit Pro', created: 'Mar 5, 2026', modified: 'Mar 8, 2026', rep: 'RV', rosterDone: 0, rosterTotal: 18, status: 'design-complete', color: '#059669', yoke: '#064e3b' },
// ];

const ORDERS = [
  { id: 'JOG20252', name: 'Calgary Storm – Home', style: 'Tampa Bay-2', submitted: 'Mar 9, 2026', qty: 20, status: 'approved', statusStep: 2, color: '#7c3aed', yoke: '#1a0a2e', tracking: 'DHL-293847561', carrier: 'DHL Express' },
  { id: 'JOG20248', name: 'Eastside Eagles – Home', style: 'Tampa Bay-2', submitted: 'Mar 1, 2026', qty: 14, status: 'in-production', statusStep: 3, color: '#1d4ed8', yoke: '#0a0e1a', tracking: null, carrier: null },
  { id: 'JOG20241', name: 'Ridgemont Raiders', style: 'Big Hit Pro', submitted: 'Feb 25, 2026', qty: 18, status: 'shipped', statusStep: 5, color: '#059669', yoke: '#064e3b', tracking: 'FEDEX-948203841', carrier: 'FedEx International' },
  { id: 'JOG20235', name: 'Northview Wolves – Away', style: 'Tampa Bay-2', submitted: 'Feb 18, 2026', qty: 22, status: 'delivered', statusStep: 6, color: '#dc2626', yoke: '#111827', tracking: 'DHL-188302874', carrier: 'DHL Express' },
  { id: 'JOG20228', name: 'Metro Hawks', style: 'Big Hit Pro', submitted: 'Feb 10, 2026', qty: 10, status: 'under-review', statusStep: 1, color: '#0891b2', yoke: '#0c2340', tracking: null, carrier: null },
  { id: 'JOG20219', name: 'Prairie Thunder', style: 'Classic Pro V2', submitted: 'Feb 2, 2026', qty: 25, status: 'qc-complete', statusStep: 4, color: '#d97706', yoke: '#1c1917', tracking: null, carrier: null },
  { id: 'JOG20210', name: 'Valley Bears', style: 'Tampa Bay-2', submitted: 'Jan 28, 2026', qty: 16, status: 'submitted', statusStep: 0, color: '#374151', yoke: '#111827', tracking: null, carrier: null },
  { id: 'JOG20199', name: 'Summit Kings', style: 'Big Hit Pro', submitted: 'Jan 15, 2026', qty: 30, status: 'delivered', statusStep: 6, color: '#be185d', yoke: '#1a0010', tracking: 'UPS-203948175', carrier: 'UPS Worldwide' },
];

const STATUS_TIMELINE = [
  { key: 'submitted',    label: 'Submitted',    icon: 'bi-clipboard-check' },
  { key: 'under-review', label: 'Under Review', icon: 'bi-search' },
  { key: 'approved',     label: 'Approved',     icon: 'bi-check-circle' },
  { key: 'in-production',label: 'Production',   icon: 'bi-gear' },
  { key: 'qc-complete',  label: 'QC Complete',  icon: 'bi-shield-check' },
  { key: 'shipped',      label: 'Shipped',      icon: 'bi-truck' },
  { key: 'delivered',    label: 'Delivered',    icon: 'bi-trophy' },
];

const DRAFT_STATUS_MAP = {
  'draft':          { cls: 'sb-draft',          label: 'Draft' },
  'design-complete':{ cls: 'sb-design-complete', label: 'Design Complete' },
  'roster-progress':{ cls: 'sb-roster-progress', label: 'Roster In Progress' },
  'ready':          { cls: 'sb-ready',           label: 'Ready to Submit' },
};
const ORDER_STATUS_MAP = {
  'submitted':    { cls: 'sb-submitted',    label: 'Submitted' },
  'under-review': { cls: 'sb-under-review', label: 'Under Review' },
  'approved':     { cls: 'sb-approved',     label: 'Approved' },
  'in-production':{ cls: 'sb-in-production',label: 'In Production' },
  'qc-complete':  { cls: 'sb-qc-complete',  label: 'QC Complete' },
  'shipped':      { cls: 'sb-shipped',      label: 'Shipped' },
  'delivered':    { cls: 'sb-delivered',    label: 'Delivered' },
};

const SAMPLE_ROSTER = [
  { no:1, name:'Olivia Browning', size:'M',  jersey:'12', flag:'🇨🇦', ca:'C' },
  { no:2, name:'Jake Morrison',   size:'L',  jersey:'7',  flag:'🇨🇦', ca:'A' },
  { no:3, name:'Ryan Callahan',   size:'XL', jersey:'19', flag:'🇺🇸', ca:'' },
  { no:4, name:'Sarah Park',      size:'S',  jersey:'4',  flag:'🇨🇦', ca:'' },
  { no:5, name:'Mike Delacroix',  size:'L',  jersey:'23', flag:'🇫🇷', ca:'' },
];

/* ═══════════════════════════════════════════════════════════
   RENDER DRAFT CARDS
═══════════════════════════════════════════════════════════ */
function renderDraftCards(data) {
  const grid = document.getElementById('draft-grid');
  document.getElementById('draft-count').textContent = data.length + ' draft' + (data.length!==1?'s':'');
  if (!data.length) {
    grid.innerHTML = `<div class="col-12"><div class="empty-state">
      <i class="bi bi-pencil-square"></i>
      <h4>No Drafts Found</h4>
      <p>Start a new design in the 3D Configurator to save it here.</p>
    </div></div>`;
    return;
  }
  grid.innerHTML = data.map(d => {
    const isUsed = parseInt(d.is_used) === 1;
    const status = isUsed ? 'design-complete' : 'draft';
    const s = DRAFT_STATUS_MAP[status];
    const ctaText = isUsed ? 'Continue to Roster' : 'Resume Design';
    const ctaClass = isUsed ? 'btn-roster' : '';
    const ctaIcon = isUsed ? 'bi-arrow-right-circle' : 'bi-pencil';
    return `
    <div class="col-12 col-md-6 col-xl-3 draft-card-col" data-status="${status}" data-name="${d.name.toLowerCase()}">
      <div class="draft-card">
        <div class="card-thumb">
          <img class="jersey-img" src="${D_BASE_URL}admin/uploads/${d.jersey_style_image}" />
          <span class="style-badge">New</span>
          <div class="thumb-actions deleteDraftsItems">
              <button class="dropdown-item-custom danger" data-draft-id="${d.draft_id}" onclick="deleteDraft(this)">
              <figure class="my-0"><img src="images/vector/trashredIcon.png" alt=""></figure>
              </button>
          </div>
        </div>
        <div class="card-body-inner">
          <div class="card-title-row">
            <div class="card-team-name">${d.name}</div>
            <div class="rep-avatar">${d.insert_date.slice(0, 10)}</div>
          </div>
          <div>
            <span class="status-badge ${s.cls}">${s.label}</span>

          </div>
          <div class="meta-row">
            <div class="meta-item"><i class="bi bi-calendar3"></i><b>Created:</b> ${d.insert_date.slice(0, 10)}</div>
            <div class="meta-item"><i class="bi bi-clock"></i><b>Modified:</b> ${d.updated_date.slice(0, 10)}</div>
          </div>
          
        </div>
        
        <div class="card-footer-inner">
          <a 
          href="${D_BASE_URL}customize.php?cat=${d.cat_enc}&subcat=${d.subcat_enc}&style=${d.style_id}&draft=${d.draft_enc}"
          target="_blank"
          class="btn-primary-cta ${ctaClass}">
            <i class="bi ${ctaIcon}"></i>${ctaText}
          </a>
        </div>
      </div>
    </div>`;
  }).join('');
}


// <div class="card-footer-inner">
//           <button class="btn-primary-cta ${ctaClass}" data-toast="${ctaText} for ${d.name}" onclick="handleToast(this)">
//             <i class="bi ${ctaIcon}"></i>${ctaText}
//           </button>
//         </div>
/* ═══════════════════════════════════════════════════════════
   RENDER ORDER CARDS
═══════════════════════════════════════════════════════════ */
function renderOrderCards(data) {
  var grid = document.getElementById('orders-grid');
  document.getElementById('orders-count').textContent =
    data.length + ' order' + (data.length !== 1 ? 's' : '');

  if (!data.length) {
    grid.innerHTML = '<div class="col-12"><div class="empty-state">'
      + '<i class="bi bi-box-seam"></i><h4>No Orders Found</h4>'
      + '<p>Submitted orders will appear here once roster is complete.</p>'
      + '</div></div>';
    return;
  }

  // Build DOM nodes — NO string concatenation with onclick, NO quote nesting
  grid.innerHTML = '';
  data.forEach(function(o) {
    var s = ORDER_STATUS_MAP[o.status] || ORDER_STATUS_MAP['submitted'];
    var r = parseInt(o.color.slice(1,3), 16);
    var g = parseInt(o.color.slice(3,5), 16);
    var b = parseInt(o.color.slice(5,7), 16);

    // ── outer col
    var col = document.createElement('div');
    col.className = 'col-12 col-md-6 col-xl-4 order-card-col';
    col.dataset.status = o.status;
    col.dataset.name = o.name.toLowerCase();

    // ── card
    var card = document.createElement('div');
    card.className = 'order-card';
    card.addEventListener('click', function() { openOrderPanel(o.id); });

    // ── thumbnail
    var thumb = document.createElement('div');
    thumb.className = 'card-thumb';
    thumb.style.background = 'linear-gradient(160deg,rgba('+r+','+g+','+b+',.13) 0%,rgba('+r+','+g+','+b+',.05) 100%)';

    var wrap = document.createElement('div');
    wrap.className = 'jersey-wrap';
    var img = document.createElement('img');
    img.src = jerseyPlaceholder(o.color, o.yoke);
    img.alt = o.name;
    wrap.appendChild(img);

    var idBadge = document.createElement('span');
    idBadge.className = 'oc-order-id';
    idBadge.textContent = o.id;

    var statusWrap = document.createElement('span');
    statusWrap.className = 'oc-status-badge';
    var statusBadge = document.createElement('span');
    statusBadge.className = 'status-badge ' + s.cls;
    statusBadge.textContent = s.label;
    statusWrap.appendChild(statusBadge);

    thumb.appendChild(wrap);
    thumb.appendChild(idBadge);
    thumb.appendChild(statusWrap);

    // ── body
    var body = document.createElement('div');
    body.className = 'card-body-inner';
    body.style.cssText = 'padding:14px 16px 10px;gap:6px;';
    body.innerHTML = '<div style="font-family:var(--font-head);font-size:17px;font-weight:700;color:var(--text-main);line-height:1.2;">'
      + o.name + '</div>'
      + '<div style="font-size:12px;color:var(--text-muted);margin-top:1px;">' + o.style + '</div>'
      + '<div class="meta-row" style="margin-top:6px;">'
      + '<div class="meta-item"><i class="bi bi-calendar3"></i>' + o.submitted + '</div>'
      + '<span class="qty-chip"><i class="bi bi-people"></i>' + o.qty + ' jerseys</span>'
      + '</div>';

    // ── footer
    var footer = document.createElement('div');
    footer.className = 'card-footer-inner';

    // View PDF button
    var pdfBtn = document.createElement('button');
    pdfBtn.className = 'btn-primary-cta';
    pdfBtn.style.flex = '1';
    pdfBtn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i>View PDF';
    pdfBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      showToast('Opening PDF for ' + o.id);
    });

    // Three-dot dropdown wrapper
    var ddWrap = document.createElement('div');
    ddWrap.className = 'custom-dropdown';

    var ddId = 'odd-' + o.id;
    var dotsBtn = document.createElement('button');
    dotsBtn.className = 'btn-dots';
    dotsBtn.innerHTML = '⋮';
    dotsBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      toggleDropdown(e, ddId);
    });

    var menu = document.createElement('div');
    menu.className = 'custom-dropdown-menu';
    menu.id = ddId;

    function makeMenuItem(icon, label, handler) {
      var btn = document.createElement('button');
      btn.className = 'dropdown-item-custom';
      btn.innerHTML = '<i class="bi ' + icon + '"></i>' + label;
      btn.addEventListener('click', function(e) { e.stopPropagation(); handler(); });
      return btn;
    }

    menu.appendChild(makeMenuItem('bi-eye',        'View Detail',      function() { openOrderPanel(o.id); }));
    menu.appendChild(makeMenuItem('bi-list-ul',    'View Roster',      function() { showToast('Roster view opened'); }));
    if (o.tracking) {
      menu.appendChild(makeMenuItem('bi-truck',    'Track Shipment',   function() { showToast('Opening carrier tracking page'); }));
    }
    menu.appendChild(makeMenuItem('bi-arrow-repeat','Reorder',         function() { showToast('Reorder draft created!'); }));
    menu.appendChild(makeMenuItem('bi-receipt',    'Download Invoice',  function() { showToast('Invoice downloading'); }));

    ddWrap.appendChild(dotsBtn);
    ddWrap.appendChild(menu);
    footer.appendChild(pdfBtn);
    footer.appendChild(ddWrap);

    card.appendChild(thumb);
    card.appendChild(body);
    card.appendChild(footer);
    col.appendChild(card);
    grid.appendChild(col);
  });
}

/* ═══════════════════════════════════════════════════════════
   ORDER DETAIL PANEL
═══════════════════════════════════════════════════════════ */
function openOrderPanel(orderId) {
  const o = ORDERS.find(x => x.id === orderId);
  if (!o) return;
  const s = ORDER_STATUS_MAP[o.status];

  document.getElementById('panel-order-id').textContent = o.id;
  document.getElementById('panel-order-name').textContent = o.name;
  document.getElementById('panel-status-badge').innerHTML =
    `<span class="status-badge ${s.cls}">${s.label}</span>`;

  // Build timeline
  const timelineHTML = `<div class="status-timeline">` +
    STATUS_TIMELINE.map((step, i) => {
      const cls = i < o.statusStep ? 'done' : i === o.statusStep ? 'current' : '';
      const icon = i < o.statusStep ? 'bi-check-lg' : step.icon;
      return `<div class="timeline-step ${cls}">
        <div class="timeline-dot"><i class="bi ${icon}"></i></div>
        <div class="timeline-label">${step.label}</div>
      </div>`;
    }).join('') + `</div>`;

  // Jersey previews
  const views = ['Front', 'Back', 'Left', 'Right'];
  const previewsHTML = `<div class="jersey-preview-grid">` +
    views.map(v => `<div class="jersey-preview-item">
      <img src="${jerseyPlaceholder(o.color, o.yoke)}" alt="${v} view" />
      <div class="view-label">${v}</div>
    </div>`).join('') + `</div>`;

  // Roster table
  const rosterHTML = `<div class="roster-table-wrap">
    <table class="roster-table">
      <thead><tr>
        <th>#</th><th>Player Name</th><th>Size</th><th>No.</th><th>Flag</th><th>C/A</th>
      </tr></thead>
      <tbody>` +
    SAMPLE_ROSTER.map(r => `<tr>
      <td>${r.no}</td>
      <td>${r.name}</td>
      <td><span class="qty-chip">${r.size}</span></td>
      <td><strong>${r.jersey}</strong></td>
      <td><span class="flag-chip">${r.flag}</span></td>
      <td>${r.ca ? `<span class="captain-badge">${r.ca}</span>` : '—'}</td>
    </tr>`).join('') +
    `</tbody></table></div>`;

  // Tracking section
  const trackingHTML = o.tracking ? `
    <div class="panel-section-title">Shipment Tracking</div>
    <div class="tracking-card">
      <i class="bi bi-truck"></i>
      <div class="tracking-info">
        <div class="track-label">Tracking Number</div>
        <div class="track-num">${o.tracking}</div>
        <div class="track-carrier">${o.carrier}</div>
      </div>
      <button class="btn-track" data-toast="Opening carrier tracking page…" onclick="handleToast(this)">
        <i class="bi bi-box-arrow-up-right"></i>Track
      </button>
    </div>` : `
    <div class="panel-section-title">Shipment Tracking</div>
    <div style="background:#f9fafb;border:1px dashed #e5e7eb;border-radius:8px;padding:16px;text-align:center;color:var(--text-muted);font-size:13.5px;">
      <i class="bi bi-clock me-2"></i>Tracking will be available once the order has shipped.
    </div>`;

  document.getElementById('panel-body').innerHTML = `
    <div class="panel-section-title">Order Timeline</div>
    ${timelineHTML}

    <div class="pdf-download-card" style="margin-top:4px;">
      <div class="pdf-icon-wrap"><i class="bi bi-file-earmark-pdf"></i></div>
      <div class="pdf-info">
        <div class="pdf-title">Art Approval Document</div>
        <div class="pdf-sub">${o.id}_ArtApproval.pdf · Generated ${o.submitted}</div>
      </div>
      <button class="btn-pdf-download" data-toast="PDF downloading…" onclick="handleToast(this)">
        <i class="bi bi-download"></i>Download
      </button>
    </div>

    <div class="panel-section-title" style="margin-top:20px;">Jersey Preview</div>
    ${previewsHTML}

    <div class="panel-section-title">Roster (${o.qty} Jerseys)</div>
    ${rosterHTML}

    <div style="margin-top:20px;">${trackingHTML}</div>

    <div style="margin-top:20px;">
      <div class="panel-section-title">Order Details</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        ${[
          ['Order ID', o.id],
          ['Style', o.style],
          ['Date Submitted', o.submitted],
          ['Total Quantity', o.qty + ' jerseys'],
          ['Status', s.label],
          ['Submitted By', 'Ravish (rep)'],
        ].map(([k,v]) => `<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px;">
          <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--text-light);margin-bottom:4px;">${k}</div>
          <div style="font-size:14px;font-weight:600;color:var(--text-main);">${v}</div>
        </div>`).join('')}
      </div>
    </div>
  `;

  document.getElementById('order-detail-overlay').classList.add('open');
  document.getElementById('order-detail-panel').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeOrderPanel() {
  document.getElementById('order-detail-overlay').classList.remove('open');
  document.getElementById('order-detail-panel').classList.remove('open');
  document.body.style.overflow = '';
}

/* ═══════════════════════════════════════════════════════════
   TAB SWITCHING
═══════════════════════════════════════════════════════════ */
function switchTab(tab) {
  ['draft','orders'].forEach(t => {
    document.getElementById('section-' + t).classList.toggle('active', t === tab);
    document.getElementById('tab-' + t).classList.toggle('active', t === tab);
    document.getElementById('nav-' + t).classList.toggle('active', t === tab);
  });
  document.getElementById('topbar-title').textContent = tab === 'draft' ? '3D Draft' : '3D Orders';
}

let DRAFTS = []; 

document.addEventListener('DOMContentLoaded', () => {

  const draftsUrl = "<?= OLS_BASE_URL ?>ajax/get_drafts_proxy.php";
  console.log("[Drafts] DOMContentLoaded fired. Fetching:", draftsUrl);

  fetch(draftsUrl)
    .then((res) => {
      if (!res.ok) {
        throw new Error("HTTP " + res.status + " " + res.statusText);
      }
      return res.json();
    })
    .then((data) => {
      console.log("Fetched drafts:", data);
      if (!data || data.status != 1 || !data.data) {
        console.warn("API returned no drafts:", data);
        renderDraftCards([]);
        return;
      }
      DRAFTS = data.data;
      renderDraftCards(DRAFTS);
    })
    .catch((err) => {
      console.error("Failed to load drafts:", err);
      renderDraftCards([]);
    });

  renderOrderCards(ORDERS);
});

/* ═══════════════════════════════════════════════════════════
   FILTERS
═══════════════════════════════════════════════════════════ */
function filterDrafts() {
  const q = document.getElementById('draft-search').value.toLowerCase();
  const st = document.getElementById('draft-filter-status').value;
  let data = DRAFTS.filter(d => {
    const status = parseInt(d.is_used) === 1 ? 'design-complete' : 'draft';
    return (!q || d.name.toLowerCase().includes(q)) &&
           (!st || status === st);
  });
  const sort = document.getElementById('draft-sort').value;
  if (sort === 'name') data.sort((a,b) => a.name.localeCompare(b.name));
  renderDraftCards(data);
}

function filterOrders() {
  const q = document.getElementById('orders-search').value.toLowerCase();
  const st = document.getElementById('orders-filter-status').value;
  let data = ORDERS.filter(o =>
    (!q || o.name.toLowerCase().includes(q) || o.id.toLowerCase().includes(q) || o.style.toLowerCase().includes(q)) &&
    (!st || o.status === st)
  );
  renderOrderCards(data);
}

/* ═══════════════════════════════════════════════════════════
   DROPDOWN TOGGLE
═══════════════════════════════════════════════════════════ */
function toggleDropdown(e, id) {
  e.stopPropagation();
  const all = document.querySelectorAll('.custom-dropdown-menu');
  const target = document.getElementById(id);
  all.forEach(m => { if (m !== target) m.classList.remove('open'); });
  target.classList.toggle('open');
}
document.addEventListener('click', () => {
  document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.remove('open'));
});

/* ═══════════════════════════════════════════════════════════
   TOAST NOTIFICATIONS
═══════════════════════════════════════════════════════════ */
function showToast(msg, icon = 'bi-check-circle-fill') {
  const wrap = document.getElementById('toast-wrap');
  const t = document.createElement('div');
  t.className = 'toast-msg';
  t.innerHTML = `<i class="bi ${icon}"></i>${msg}`;
  wrap.appendChild(t);
  setTimeout(() => { t.style.opacity='0'; t.style.transform='translateY(6px)'; t.style.transition='all .25s'; setTimeout(()=>t.remove(), 280); }, 2600);
}

/* ═══════════════════════════════════════════════════════════
   DELETE DRAFT
═══════════════════════════════════════════════════════════ */
function deleteDraft(btn) {
  const draftId = btn.dataset.draftId;
  if (!draftId) return;

  const card = btn.closest('.draft-card-col');

  swal({
    title: 'Delete Draft?',
    text: 'Are you sure you want to delete this draft? This action cannot be undone.',
    icon: 'warning',
    buttons: {
      cancel: {
        text: 'No',
        value: null,
        visible: true,
        className: 'btn btn-danger'
      },
      confirm: {
        text: 'Yes, Delete',
        value: true,
        visible: true,
        className: 'btn btn-primary'
      }
    }
  }).then(confirmed => {
    if (!confirmed) return;

    const body = new URLSearchParams();
    body.append('draft_id', draftId);

    fetch('<?= OLS_BASE_URL ?>ajax/delete_draft_proxy.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString()
    })
    .then(res => res.json())
    .then(data => {
      if (data && data.status === 200) {
        if (card) card.remove();
        DRAFTS = DRAFTS.filter(d => String(d.draft_id) !== String(draftId));
        document.getElementById('draft-count').textContent =
          DRAFTS.length + ' draft' + (DRAFTS.length !== 1 ? 's' : '');
        swal({
          title: 'Deleted!',
          text: 'Draft has been deleted successfully.',
          icon: 'success',
          button: { text: 'OK', className: 'btn btn-primary' }
        });
      } else {
        swal({
          title: 'Error',
          text: (data && data.msg) ? data.msg : 'Failed to delete draft.',
          icon: 'error',
          button: { text: 'OK', className: 'btn btn-primary' }
        });
      }
    })
    .catch(() => {
      swal({
        title: 'Error',
        text: 'Something went wrong. Please try again.',
        icon: 'error',
        button: { text: 'OK', className: 'btn btn-primary' }
      });
    });
  });
}

/* ═══════════════════════════════════════════════════════════
   INIT
═══════════════════════════════════════════════════════════ */

</script>