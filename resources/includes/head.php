<?php require_once 'default.php'; ?>

<meta charset="utf-8" />

<meta http-equiv="X-UA-Compatible" content="IE=Edge" />	

<!-- Favicon -->
<link rel="shortcut icon" type="image/png" href="/images/fav_icon.png">

<!-- Apple/Safari icon -->
<link rel="apple-touch-icon" sizes="180x180" href="/images/fav_icon.png">

<!-- Square Windows tiles -->
<meta name="msapplication-square70x70logo" content="/images/fav_icon.png">
<meta name="msapplication-square150x150logo" content="/images/fav_icon.png">
<meta name="msapplication-square310x310logo" content="/images/fav_icon.png">

<!-- Rectangular Windows tile -->
<meta name="msapplication-wide310x150logo" content="/images/fav_icon.png">

<!-- Windows tile theme color -->
<meta name="msapplication-TileColor" content="white">

<?php
echo createCSSLink('games');
echo createCSSLink('home');
echo createCSSLink('page');
echo createCSSLink('scores');

function createCSSLink(string $fileName)
{
    $version = 'v1.1.0';
    $fileLocation = STYLE . '/' . $fileName . '_' . $version . '.css';

    return "<link href='$fileLocation' type='text/css' rel='stylesheet' /> \n";
}
?>
