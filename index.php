<?php

define("DEBUG_MODE", false);
define("APP_VERSION", "2.0");

if (!(isset($isApi) && $isApi == true)) {
    include "views/main.html";
}