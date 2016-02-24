<?php

define("DEBUG_MODE", true);
define("APP_VERSION", "2.0");

if (!(isset($isApi) && $isApi == true)) {
    include "views/main.html";
}