<?php

require_once './vendor/autoload.php';
require_once '../../cmsimple/classes/CSRFProtection.php';
require_once '../../cmsimple/functions.php';

require_once '../plib/classes/Request.php';
require_once '../plib/classes/Response.php';
require_once '../plib/classes/SystemChecker.php';
require_once '../plib/classes/Url.php';
require_once '../plib/classes/View.php';
require_once '../plib/classes/FakeRequest.php';
require_once '../plib/classes/FakeSystemChecker.php';

require_once './classes/model/TextBlocks.php';
require_once './classes/AdminController.php';
require_once './classes/BoilerplateController.php';
require_once './classes/Dic.php';
require_once './classes/InfoController.php';

const CMSIMPLE_XH_VERSION = "1.8";
const CMSIMPLE_URL = "http://example.com/";
const BOILERPLATE_VERSION = "2.1-dev";
