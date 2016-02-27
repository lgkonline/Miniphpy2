<?php

define("DEBUG_MODE", false);
define("APP_VERSION", "2.0.1");

if (!(isset($isApi) && $isApi == true)) {
    include "views/main.html";
}