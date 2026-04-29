<?php
require_once 'get-display-map.php';

$data = json_decode(file_get_contents("php://input"), true);

$designId    = $data['design_id'];
$color       = $data['color'];
$textDecals  = $data['textDecals'];
$imageDecals = $data['imageDecals'];

$displayMap = getDisplayMap($designId);

$svgPath = "https://3d.jog-joinourgame.com/jogdigital_test/admin/assets/svg/".$designId."/Calgary.svg";

/**
 * Fetch remote file using cURL
 */
function getRemoteFile($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    // Optional (if SSL issues)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
    }

    curl_close($ch);

    return $response;
}

$svg = getRemoteFile($svgPath);

/**
 * Validate SVG before parsing
 */
if (empty($svg)) {
    die("❌ Failed to fetch SVG file from: " . $svgPath);
}

libxml_use_internal_errors(true);

$dom = new DOMDocument();

if (!$dom->loadXML($svg)) {
    echo "❌ Invalid SVG XML\n";
    print_r(libxml_get_errors());
    exit;
}

$xpath = new DOMXPath($dom);

/* ---------- TEXT UPDATE ---------- */

/* ---------- PLAYER NAME (SAFE SVG TEXT INSERT) ---------- */
$xpath->registerNamespace("svg", "http://www.w3.org/2000/svg");

$svgRoot = $dom->documentElement;

$font1 = base64_encode(file_get_contents("../Fonts/SuperstarM54JOG.ttf"));
$font2 = base64_encode(file_get_contents("../Fonts/JerseyM54Jog3.ttf"));
$PROBLOCK = base64_encode(file_get_contents("../Fonts/PROBLOCK.ttf"));
$NHLChicago = base64_encode(file_get_contents("../Fonts/NHLChicago.ttf"));
$HaettenschweilerJog4 = base64_encode(file_get_contents("../Fonts/HaettenschweilerJog4.ttf"));

$style = "
@font-face {
  font-family: 'SuperstarM54JOG';
  src: url(data:font/ttf;base64,$font1) format('truetype');
}
@font-face {
  font-family: 'JerseyM54Jog3';
  src: url(data:font/ttf;base64,$font2) format('truetype');
}
@font-face {
  font-family: 'PROBLOCK';
  src: url(data:font/ttf;base64,$PROBLOCK) format('truetype');
}
@font-face {
  font-family: 'NHLChicago';
  src: url(data:font/ttf;base64,$NHLChicago) format('truetype');
}
@font-face {
  font-family: 'HaettenschweilerJog4';
  src: url(data:font/ttf;base64,$HaettenschweilerJog4) format('truetype');
}
";
$styleNode = $dom->createElementNS(
    "http://www.w3.org/2000/svg",
    "style"
);
$styleNode->appendChild($dom->createCDATASection($style));
$svgRoot->insertBefore($styleNode, $svgRoot->firstChild);

// Read viewBox
$viewBox = preg_split('/\s+/', trim($svgRoot->getAttribute('viewBox')));
$vbWidth  = (float)$viewBox[2];
$vbHeight = (float)$viewBox[3];

$usedDisplays = [];

// from text decals
foreach ($textDecals as $t) {
    if (!empty($t['displayName'])) {
        $usedDisplays[$t['displayName']] = true;
    }
}

// from image decals
foreach ($imageDecals as $i) {
    if (!empty($i['displayName'])) {
        $usedDisplays[$i['displayName']] = true;
    }
}

foreach ($displayMap as $displayName => $map) {

    // If this displayName was NOT sent in text or image decals
    if (!isset($usedDisplays[$displayName])) {

        foreach ($map['items'] as $targetId => $cfg) {

            $group = $xpath->query("//*[@id='$targetId']")->item(0);
            if (!$group) continue;

            // remove everything inside the target group
            while ($group->firstChild) {
                $group->removeChild($group->firstChild);
            }
        }
    }
}


foreach ($textDecals as $text) {
    
    $value   = $text['text'];
    $colortext = $text['color'];
    $size    = $text['fontSize'];
    $font    = $text['fontFamily'];
    $display = $text['displayName'];
    $outlineColor = $text['outlineColor'];
    $outlineWidth = $text['outlineWidth'];



    if (!isset($displayMap[$display])) continue;
    $fullNumber = $value;  // eg "25"

    $firstDigit = substr($fullNumber, 0, 1);  // "2"
    $lastDigit  = substr($fullNumber, -1);    // "5"
    foreach ($displayMap[$display]['items'] as $targetId => $cfg) {

        $group = $xpath->query("//*[@id='$targetId']")->item(0);
        if (!$group) continue;

        while ($group->firstChild) {
            $group->removeChild($group->firstChild);
        }

        $svgX = $vbWidth  / $cfg['x'];
        $svgY = $vbHeight / $cfg['y'];
        $sizefont =  $cfg['size'] ?? $size;
        $rotate = $cfg['rotate'];

        $textNode = $dom->createElementNS(
            "http://www.w3.org/2000/svg",
            "text"
        );

        $printValue = $value; // default (for front/back chest numbers)

        if ($display === "Right Sleeve Number" || $display === "Left  Sleeve Number" || $display === "Sleeve Number Right" || $display === "Sleeve Number Left" || $display === "Sleeves Number Left" || $display === "Sleeve Left" || $display === "Sleeve Right") {

            if ($targetId === "f_rightnumber")  $printValue = $firstDigit;
            if ($targetId === "f_leftnumber")   $printValue = $lastDigit;

            if ($targetId === "b_rightnumber")  $printValue = $lastDigit;
            if ($targetId === "b_leftnumber")   $printValue = $firstDigit;
        }

        $textNode->appendChild(
            $dom->createTextNode($printValue)
        );

        $textNode->setAttribute("x", $svgX);
        $textNode->setAttribute("y", $svgY);
        $textNode->setAttribute("text-anchor", "middle");
        $textNode->setAttribute("dominant-baseline", "middle");

        // $textNode->setAttribute("fill", $colortext);
        // $textNode->setAttribute("font-size", $sizefont);
        // $textNode->setAttribute("font-family", $font);

        $textNode->setAttribute("fill", $colortext);

        // ✅ Outline (Stroke)
        $textNode->setAttribute("stroke", $outlineColor); // outline color
        $textNode->setAttribute("stroke-width", $outlineWidth/3); // adjust as needed
        $textNode->setAttribute("paint-order", "stroke"); // important

        $textNode->setAttribute("font-size", $sizefont);
        $textNode->setAttribute("font-family", $font);
        $textNode->setAttribute(
            "transform",
            "rotate($rotate $svgX $svgY)"
        );

        $group->appendChild($textNode);
    }
}

foreach ($imageDecals as $imgDecal) {

    $display = $imgDecal['displayName'];
    $imageUrl = $imgDecal['imageSrc'] ?? null;

    //print_r($display);

    $imageBase64 = null;

    if ($imageUrl) {

        // If already base64, use it directly
        if (strpos($imageUrl, 'data:image') === 0) {
            $imageBase64 = $imageUrl;
        } 
        else {
            // Otherwise treat as file path
            $path = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($imageUrl, '/');

            if (file_exists($path)) {
                $mime = mime_content_type($path);
                $imageBase64 = "data:$mime;base64," . base64_encode(file_get_contents($path));
            }
        }
    }


    foreach ($displayMap[$display]['items'] as $targetId => $cfg) {

        $group = $xpath->query("//*[@id='$targetId']")->item(0);
        if (!$group) continue;

        while ($group->firstChild) {
            $group->removeChild($group->firstChild);
        }        
        $svgX = $vbWidth  / $cfg['x'];
        $svgY = $vbHeight / $cfg['y'];
        $rotate = $cfg['rotate'] ?? 0;

        $img = $dom->createElementNS("http://www.w3.org/2000/svg", "image");

        // 🔥 SVG 2.0 compatible
        $img->setAttribute("href", $imageBase64);
        
        $img->setAttribute("x", $svgX - 50);
        $img->setAttribute("y", $svgY - 50);
        $img->setAttribute("width",  $cfg['width']?$cfg['width']:30);
        $img->setAttribute("height",  $cfg['height']?$cfg['height']:30);

        if ($rotate != 0) {
            $img->setAttribute(
                "transform",
                "rotate($rotate $svgX $svgY)"
            );
        }

        $group->appendChild($img);
    }
}



foreach ($color as $part => $col) {
    // Find both front and back meshes
    $nodes = $xpath->query("//*[@id='$part' or @id='{$part}_1' or @id='{$part}_2' or @id='{$part}_3' or @id='{$part}_4' or @id='{$part}_5' ]");
    foreach ($nodes as $partNode) {
        // Remove old SVG shapes inside
        while ($partNode->firstChild) {
            $partNode->removeChild($partNode->firstChild);
        }
        // Remove Illustrator classes
        if ($partNode->hasAttribute('class')) {
            $partNode->removeAttribute('class');
        }
        // Apply color
        $partNode->setAttribute("fill", $col);
    }
}

/* ---------- SAVE FINAL SVG ---------- */
$svgOutput = $dom->saveXML();
echo json_encode([
    "status" => "success",
    "svg"    => $svgOutput
]);
