<?php
/**
 * [BEGIN_COT_EXT]
 * Hooks=global
 * [END_COT_EXT]
 */
 
/**
 * getlastposts.global.php - // Global hook, includes dependencies
 *
 * getlastposts plugin for CMF Cotonti (latest for today), PHP 8.4+, MySQL 8.0+
 * Date: Jun 6Th, 2026
 * Filename: getlastposts.global.php
 *
 * Source: https://github.com/webitproff/getlastposts-cotonti
 * Demo: https://abuyfile.com/
 *
 * @package getlastposts
 * @version 1.3.1
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff
 * @license BSD
 */	
 
 
defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('forums', 'module');
require_once cot_langfile('getlastposts', 'plug');
require_once cot_incfile('getlastposts', 'plug'); 

