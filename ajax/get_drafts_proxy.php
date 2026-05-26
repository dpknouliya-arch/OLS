<?php
include('../db.php');
include('../check-session.php');

header('Content-Type: application/json');

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id  = (int) $obj_user->user_id;
// $brand_id is already resolved by db.php via get_ols_brand_id()

$res = callAPI("get_drafts.php?user_id=$user_id&brand_id=$brand_id");

// Client-side safety net: strip any cross-brand drafts the API might return
if (!empty($res['data']) && is_array($res['data'])) {
    $res['data'] = array_values(array_filter($res['data'], function ($d) use ($brand_id) {
        return !isset($d['brand_id']) || (int)$d['brand_id'] === $brand_id;
    }));
}

echo json_encode($res);
