<!--
	/********************************************************************************
	* File: getlastposts.sidebar.tpl  # Default output template
	* Extension: getlastposts
	* Description: HTML template for cot_forums_getLastPosts 
	* Compatibility: CMF/CMS Cotonti (https://github.com/Cotonti/Cotonti)
	* Dependencies: 
	* 		 Bootstrap 5.3.+[](https://getbootstrap.com/); 
	* 		 Font Awesome Free 7.1[](https://fontawesome.com/)
	* Theme: index36 
	* Version: 1.3.1 
	* Created: 06 Jun 2026 
	* Updated: 07 Jun 2026 
	* Copyright (c) 2026 webitproff | https://github.com/webitproff
	* Source: https://github.com/webitproff/getlastposts-cotonti
	* Demo: https://abuyfile.com/
	* Help and support: https://abuyfile.com/ru/forums/cotonti/custom/plugs
	* License: BSD (Free distribution with saving Copyright (c) 2026 webitproff)  
	********************************************************************************/
-->

<!-- BEGIN: MAIN -->
<div class="last-posts-widget">
  <h4>{PHP.L.getlastposts_title}</h4>
  <ul class="list-unstyled">
    <!-- BEGIN: POST_ROW -->
    <li class="{POST_ROW_ODDEVEN}">
      <a href="{POST_ROW_TOPIC_URL}">{POST_ROW_TITLE}</a>
      <br><small>{POST_ROW_POSTER}, {POST_ROW_DATE}</small>
      <p class="text-muted">{POST_ROW_TEXT}</p>
    </li>
    <!-- END: POST_ROW -->
    <!-- BEGIN: NO_POSTS -->
    <li>{PHP.L.getlastposts_none}</li>
    <!-- END: NO_POSTS -->
  </ul>
</div>
<!-- END: MAIN -->