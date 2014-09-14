<?php  if(!defined('CORELOADED')) die();
//user configuration
define('TIMEZONE'              , "America/New_York");
define('IMAGE_PREPROCESSING'   , true); // false speeds up uploads, but doesn't trim borders or resize to save disk space
define('IMAGE_TRIM_BORDER'     , true); // trim whitespace from images?
define('IMAGE_BORDER_COLOR'    , null); // null turns on autodetect.  otherwise enter a 6 digit hex number like 0xFFFFFF (white)
define('IMAGE_BORDER_TOLERANCE', 5   ); // measured as euclidean distance
define('MAX_FILE_W'            , 1000); // uploaded images will be resized to less than this max width
define('MAX_FILE_H'            , 2000); // uploaded images will be resized to less than this max height
define('PAGINATE_SONG_LIST'    , true); // enable or disable splitting the songlist into pages
$genres   = array(
    "Alternative",
    "Blues",
    "Celtic",
    "Children's",
    "Classical",
    "Country",
    "Dance",
    "Easy Listening",
    "Electronica",
    "Folk",
    "Folk-Rock",
    "Hip-Hop/Rap",
    "Indie",
    "Jazz",
    "Latin",
    "Metal",
    "New Age",
    "Oldies",
    "Pop",
    "Punk",
    "R&B/Soul",
    "Reggae",
    "Religious",
    "Rock",
    "Roots",
    "Soundtracks",
    "Traditional",
    "Trad. Pop",
    "World"
);
?>