<?php
/*  UCL MediaWiki Skin
 *  Copyright (C) 2014  UCL
 *  Contains elements that are copyright of the Mediawiki foundation, and
 *   also elements that are copyright 1999-2014 UCL
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License along
 *  with this program; if not, write to the Free Software Foundation, Inc.,
 *  51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */
/**
 * UCLRC skin
 *
 * @file
 * @ingroup Skins
 * @version 0.0.1
 * @author Ian Kirker (i.kirker@ucl.ac.uk)
 * License: CCNYSA
 */

/**
 * UCLRC skin dependencies
 */
 
if ( ! defined( 'MEDIAWIKI' ) )
   die( 1 );


// I'm not sure if this is required here in this file.
require_once( dirname( dirname( __FILE__ ) ) . '/includes/SkinTemplate.php');

// initialize
if( !defined( 'MEDIAWIKI' ) ){
   die( "This is a skins file for mediawiki and should not be viewed directly.\n" );
}
 
// inherit main code from SkinTemplate, set the CSS and template filter
class SkinUCLRC extends SkinTemplate {
   var $useHeadElement = true;
 
   function initPage( OutputPage $out ) {
      parent::initPage( $out );
      $this->skinname  = 'uclrc';
      $this->stylename = 'uclrc';
      $this->template  = 'UCLRCTemplate';
   }
   function setupSkinUserCss( OutputPage $out ) {
      global $wgHandheldStyle;
      parent::setupSkinUserCss( $out );
      // Append to the default screen common & print styles...
      // This bit is currently nonfunctional, because this skin is in general a bit hacky.
      $out->addStyle( 'uclrc/main.css', 'screen' );
      if ( $wgHandheldStyle ) {
         $out->addStyle( 'uclrc/handheld.css', 'handheld' );
      }
   }
}



class UCLRCTemplate extends QuickTemplate {
   /* List of valid colour choices here:
                          *  light-blue (departments)
                          *  red
                          *  orange     (news)
                          * (some more, but I haven't found a way to list them) 
                          */
   function uclcolour() { echo "light-blue"; }
   function ucldir()    { echo "departments"; }
   //function uclcolour() { echo "orange"; }
   //function ucldir()    { echo "news"; }

   /**
    * Template filter callback for this skin.
    * Takes an associative array of data set from a SkinTemplate-based
    * class, and a wrapper for MediaWiki's localization database, and
    * outputs a formatted page.
    */
   
   public function execute() {
      global $wgRequest;
 
      $skin = $this->data['skin'];
 
      // suppress warnings to prevent notices about missing indexes in $this->data
      wfSuppressWarnings();

      global $wgVectorUseIconWatch;

      // Build additional attributes for navigation urls
      $nav = $this->data['content_navigation'];

      if ( $wgVectorUseIconWatch ) {
         $mode = $this->getSkin()->getTitle()->userIsWatching() ? 'unwatch' : 'watch';
         if ( isset( $nav['actions'][$mode] ) ) {
            $nav['views'][$mode] = $nav['actions'][$mode];
            $nav['views'][$mode]['class'] = rtrim( 'icon ' . $nav['views'][$mode]['class'], ' ' );
            $nav['views'][$mode]['primary'] = true;
            unset( $nav['actions'][$mode] );
         }
      }

      $xmlID = '';
      foreach ( $nav as $section => $links ) {
         foreach ( $links as $key => $link ) {
            if ( $section == 'views' && !( isset( $link['primary'] ) && $link['primary'] ) ) {
               $link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
            }

            $xmlID = isset( $link['id'] ) ? $link['id'] : 'ca-' . $xmlID;
            $nav[$section][$key]['attributes'] =
               ' id="' . Sanitizer::escapeId( $xmlID ) . '"';
            if ( $link['class'] ) {
               $nav[$section][$key]['attributes'] .=
                  ' class="' . htmlspecialchars( $link['class'] ) . '"';
               unset( $nav[$section][$key]['class'] );
            }
            if ( isset( $link['tooltiponly'] ) && $link['tooltiponly'] ) {
               $nav[$section][$key]['key'] =
                  Linker::tooltip( $xmlID );
            } else {
               $nav[$section][$key]['key'] =
                  Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
            }
         }
      }
      $this->data['namespace_urls'] = $nav['namespaces'];
      $this->data['view_urls'] = $nav['views'];
      $this->data['action_urls'] = $nav['actions'];
      $this->data['variant_urls'] = $nav['variants'];

      // Reverse horizontally rendered navigation elements
      if ( $this->data['rtl'] ) {
         $this->data['view_urls'] =
            array_reverse( $this->data['view_urls'] );
         $this->data['namespace_urls'] =
            array_reverse( $this->data['namespace_urls'] );
         $this->data['personal_urls'] =
            array_reverse( $this->data['personal_urls'] );
      }


      if ( !isset($this->data['sitename']) ) {
            global $wgSitename;
            $this->set( 'sitename', $wgSitename );
      }
      if ( !isset($this->data['sidebartitle']) ) {
          global $wgSidebarTitle;
          if (isset($wgSidebarTitle)) {
              $this->set( 'sidebartitle', $wgSidebarTitle );
          } else {
              $this->set( 'sidebartitle', $this->data['sitename'] );
          }
      }


      /*************************************
       * HTML BEGINS HERE OH NOES
       *************************************/


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en"
      xml:lang="en">
   <?php $this->html( 'headelement' ); ?>
   <head>
      <title><?php echo $this->data['title']; ?></title>
      <link rel="shortcut icon" href="//www.ucl.ac.uk/<?php $this->ucldir(); ?>/++resource++images/favicon2.ico" />
         <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
         <meta name="contact.name" content="UCL Research Computing" />
         <meta name="contact.email" content="rc-support@ucl.ac.uk" />

         <!--MediaWiki Common Stylesheets-->
         <?php   if($this->data['pagecss']) { ?>
         <style type="text/css"><?php $this->html('pagecss') ?></style>
         <?php   }
                 if($this->data['usercss']) { ?>
         <style type="text/css"><?php $this->html('usercss') ?></style>
         <?php   } ?>
         <!--End MediaWiki Common Stylesheets-->

         <!--Start General Layout Styles-->
            <!--Start UCL default layout 2 column styles-->
            <link href="//www.ucl.ac.uk/<?php $this->ucldir(); ?>/++resource++css/ucl_default_2col_layout.css"
                  rel="stylesheet" type="text/css" />
            <!--[if lte IE 8]><link rel="stylesheet" type="text/css" href="//www.ucl.ac.uk/departments/++resource++patches/patch_ucl_default_2col_layout.css" /><![endif]-->
            <!--End UCL default layout 2 column styles-->
            
            <!--Corporate identity styling below-->
            <link href="//www.ucl.ac.uk/<?php $this->ucldir(); ?>/++resource++css/screen/corp-identity-<?php $this->uclcolour(); ?>.css"
                  rel="stylesheet" type="text/css" />
            <!--Corporate identity styling above-->
         <!--End General Layout Styles-->

         <!--Start Print CSS-->
         <link href="//www.ucl.ac.uk/<?php $this->ucldir(); ?>/++resource++css/print/print.css"
               media="print" rel="stylesheet"
               type="text/css" />
         <!--Set IE to be print smaller width, so that it doesn't crop content-->
         <!--[if lte IE 8]>
             <style type="text/css">
                 @media print {body, div.page, #main{width:600px;}}
             </style>
         <![endif]-->
         <!--End Print CSS-->

         <!--Start JQuery Javascript-->
            <script src="//ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
            <script>window.jQuery || document.write('<script src="//www.ucl.ac.uk/news/++resource++default_scripts/jquery-1.3.2.min.js"><\/script>')</script>
         <!--End JQuery Javascript-->
        
    

         <!--Start UCL Javascript-->
            <!--<script type="text/javascript" tal:attributes="src string:${resourcebase}/++resource++default_scripts/ucl.min.js" src="/js/ucl/ucl.min.js"></script>-->
         <!--End UCL Javascript-->

         <!--Start JQuery Core User Interface Javascript-->
            <script type="text/javascript"
                    src="//www.ucl.ac.uk/<?php $this->ucldir(); ?>/++resource++default_scripts/ui.core.min.js"></script>
         <!--End JQuery Core User Interface Javascript-->

         <!--Start JQuery Font Resizer Javascript-->
            <script type="text/javascript"
                    src="//www.ucl.ac.uk/<?php $this->ucldir(); ?>/++resource++default_scripts/fontsizer.jquery.js"></script>
         <!--End JQuery Font Resizer Javascript-->

         <!--Start JQuery cookie Javascript-->
            <script type="text/javascript"
                    src="//www.ucl.ac.uk/news/++resource++default_scripts/jquery.cookie.min.js"></script>
         <!--End JQuery cookie Javascript-->




         <script type="text/javascript"
                 src="//www.ucl.ac.uk/<?php $this->ucldir(); ?>/++resource++default_scripts/jquery.doc.ready.js"></script>

         <!--Steal Silva Style Sheets-->
         <!-- <link rel="stylesheet" type="text/css" href="//www.ucl.ac.uk/isd/staff/research_services/local-styles.css"> -->
         <!-- Add a margin back to the left column. -->
         <style>
            #left-silva-content{ margin-top: 1em; }
         </style>
         <!-- This page is originally templated on the news sites but then I took some bits from the faculties website. -->
         <link rel="stylesheet" type="text/css"
               href="//www.ucl.ac.uk/<?php $this->ucldir(); ?>/local-styles.css" />

         <!-- Stylesheet for high contrast style switching -->
         <link rel="stylesheet" type="text/css"
               id="contrastCSS"
               href="//www.ucl.ac.uk/<?php $this->ucldir(); ?>/++resource++css/screen/ucl_normal_view.css" />
         <!--end Silva Style Sheets-->
         
         <!-- styling to actually make links different to h5 elements. <?php /* Idiots. */ ?> -->
         <style>
            a:link, a:visited { color: #134C92; }
         </style>

    </head>
    <body>
       <?php /* This part only displays if a user is logged in - it contains the page and user toolbars. */ ?>
       <?php global $wgUser;
             if( $wgUser->isLoggedIn()) {  ?>
       <div style="float: left; text-align: left; position: absolute;margin: 10px 10px 0px 10px;">
       <h5><?php $this->msg('personaltools') ?></h5>
       <ul class="disc">
<?php                 foreach( $this->data['personal_urls'] as $key => $item ) { ?>
                                <li id="<?php echo Sanitizer::escapeId( "pt-$key" ) ?>"<?php
                                        if ($item['active']) { ?> class="active"<?php } ?>><a href="<?php
                                echo htmlspecialchars( $item['href'] ) ?>"<?php echo $skin->tooltipAndAccesskeyAttribs('pt-'.$key) ?><?php
                                if( !empty( $item['class'] ) ) { ?> class="<?php
                                echo htmlspecialchars( $item['class'] ) ?>"<?php } ?>><?php
                                echo htmlspecialchars( $item['text'] ) ?></a></li>
<?php                   } ?>
       </ul>
       <h5>Page Tools</h5>
       <ul class="disc"><?php
          foreach( $this->data['content_actions'] as $key => $tab ) {
             echo '
                <li id="', Sanitizer::escapeId( "ca-$key" ), '"';
             if ( $tab['class'] ) {
                echo ' class="', htmlspecialchars($tab['class']), '"';
             }
             echo '><a href="', htmlspecialchars($tab['href']), '"',
                  $skin->tooltipAndAccesskeyAttribs('ca-'.$key), '>',
                  htmlspecialchars($tab['text']),
                  '</a></li>';
          }?>
       </ul>
       </div>
       <?php } /* End logged in-dependent section */ ?>
       <div class="page_margins">
          <!-- Graphic Border - Begin Part 1 -->
             <div id="border-top">
                <div id="edge-tl"> </div>
                <div id="edge-tr"> </div>
             </div>
             <!-- Graphic Border - End Part 1 -->
             <div class="page">
                <div id="printLogo">
                   <img src="//www.ucl.ac.uk/<?php $this->ucldir(); ?>/++resource++images/printlogo.gif"
                        alt="UCL logo" />
                </div>
                        
                <div id="header" class="tb-dark_grey">
                <div id="topnav">
                   <!-- start: skip link navigation -->
                   <a class="skip" href="#navigation" title="skip link">skip to navigation</a><span class="hideme">.</span>
                   <a class="skip" href="#content" title="skip link">skip to content</a><span class="hideme">.</span>
                   <!-- end: skip link navigation -->
                </div>
                <h1 class="section_header_white" style="margin-left: 1em;">
		<?php $this->text('sitename'); ?> <br /><br /><span style="font-style: italic;"><?php /* $this->msg('tagline') */ ?> </span>
                </h1>
                <span class="section_subheader_white"></span>
                <div id="logo_holder"> 
                   <a href="/"><img src="//www.ucl.ac.uk/<?php $this->ucldir(); ?>/++resource++images/corp-identity-<?php $this->uclcolour(); ?>.gif"
                                    alt="UCL Home" /></a>
                </div> 
             </div>

             <!-- Begin breadcrumbs row -->
             <div id="nav" class="noprint">
                <div class="hlist" id="corp-identity-<?php $this->uclcolour();?>">
                <!--Start Search box-->
                <div id="search">
                   <!--<label for="searchInput"><?php $this->msg('search') ?></label>-->
                   <form action="<?php $this->text('searchaction') ?>" id="googlesearch">
                            <input placeholder="Search Wiki" id="query" name="search" type="text" style="width: 100px;" />
                            <!--  This is the Go button: -->
                            <input type='submit' name="go" class="submit" value="<?php $this->msg('searcharticle'); ?>" /> 
                            <!-- And this is the search button -->
                            <input type='submit' name="fulltext" id="mw-searchButton" class="submit" value="<?php $this->msg('searchbutton') ?>" />
                   </form>
                </div>
                <!--End search box-->
                <ul width="50%">
                   <li>
                      <a href="//www.ucl.ac.uk/">UCL Home</a>
                   </li>
                   <li>
                      <a href="//www.ucl.ac.uk/isd/">ISD</a>
                   </li>
                   <li>
                      <a href="//www.ucl.ac.uk/isd/staff/research_services">RITS</a>
                   </li>
                   <li>
                      <a href="//www.ucl.ac.uk/isd/staff/research_services/research-computing">RCPS</a>
                   </li>
                   <li>
                      <a href="<?php echo htmlspecialchars($this->data['nav_urls']['mainpage']['href']) ?>" >Support Pages</a> / 
                   
                      <?php
                         // Split the components of any subpages and make them into the same format as the stupid breadcrumbs
                         $page_title = $this->data['title'];
                         $page_title_elements = explode("/", $page_title);
                         $page_title_crumbs = "";
                         $crumb_iterator = 1;
                         foreach ($page_title_elements as $page_title_element) {
                             if ( $crumb_iterator != 1 ) { $page_title_crumbs .= " / "; }
                             $up_to_here = implode("/", array_slice($page_title_elements,0,$crumb_iterator));
                             $page_title_crumbs .= "<a href=\"{$up_to_here}\">$page_title_element</a>\n ";
                             $crumb_iterator += 1;
                         }

                         global $wgTitle; 
                         if (($wgUser->isLoggedIn()) && ($wgTitle->isProtected('edit'))) {
                             $page_title_crumbs .= "<img style=\"height:10px;width:10px;\" alt=\"This page is protected.\" " .
                                                   "title=\"This page is protected.\" " .
                                                   "src=\"/mediawiki119/skins/common/images/full_protect.svg\" />";
                         }
                         echo $page_title_crumbs ;
                      ?>
                      <!--<a href="<?php ?>"><?php $this->html('title'); ?></a>  -->
                   </li>
		        </ul>
                <!--<div class="hlist" style="vertical-align:text-top; color: #ff0000;"> -->
                   <ul style="text-align: right;">
<?php                   foreach( $this->data['personal_urls'] as $key => $item ) { ?>
                                <li id="<?php echo Sanitizer::escapeId( "pt-$key" ) ?>"<?php
                                        if ($item['active']) { ?> class="active"<?php } ?>><a href="<?php
                                echo htmlspecialchars( $item['href'] ) ?>"<?php echo $skin->tooltipAndAccesskeyAttribs('pt-'.$key) ?><?php
                                if( !empty( $item['class'] ) ) { ?> class="<?php
                                echo htmlspecialchars( $item['class'] ) ?>"<?php } ?>><?php
                                echo htmlspecialchars( $item['text'] ) ?></a></li>
<?php                   } ?>
                </ul>
                <!-- </div> -->
             </div>
          </div>

          <!-- begin: main content area #main -->
             <?php //Turns on debugging messages and errors and such. 
                error_reporting(-1);
                ini_set( 'display_errors', 1); ?>
          <div id="main">
             <div>
                <!-- begin: #col1 - first float column -->
                <div id="col1">
                   <div id="col1_content" class="clearfix">
                      <div id="col_top_left" class="col_top left_coltop">
                         <!-- logo goes here? -->
                      </div>
                      <a id="navigation" name="navigation"></a>

                      <!-- Left-hand nav bar -->
                      <!--  This isn't actually silva content, I just wanted to not have to modify the stylesheet. :/ -->
                      <div id="left-silva-content"
                           class="leftcontainer">

                         <h3 class="heading">
                            <?php 
                                $this->text('sidebartitle'); 
                            ?>
                        </h3>
                         <ul class="disc"><?php /* No idea what disc stands for. */ ?>


                         <?php foreach( $this->data['sidebar'] as $bar => $cont ) { ?>
                         <div class='portlet' id='p-<?php echo Sanitizer::escapeId( $bar ) ?>'<?php echo $skin->tooltip('p-'.$bar) ?>>
                            <h5><?php $out = wfMsg( $bar ); if( wfEmptyMsg( $bar, $out ) ) echo $bar; else echo $out; ?></h5>
                            <div class='pBody'>
                               <ul>
                               <?php foreach( $cont as $key => $val ) { ?>
                                  <li id="<?php echo Sanitizer::escapeId( $val['id'] ) ?>"<?php
                                     if( $val['active'] ) { ?> class="active" <?php }
                                     ?>><a href="<?php echo htmlspecialchars( $val['href'] ) ?>"<?php echo $skin->tooltipAndAccesskeyAttribs($val['id']) ?>><?php echo htmlspecialchars( $val['text'] ) ?></a></li>
                               <?php } ?>
                               </ul>
                            </div>
                         </div>
                         <?php } ?>

                         </ul>
                         <!-- <h3 class="heading">Other links</h3> -->
                         <!-- <ul class="disc"> -->
                            <!-- -->
                         <!--   <?php $this->data['title']; ?> -->
                         <!-- </ul> -->
                         <?php  
                            // If the page isn't in any categories, don't render the header                       
                            global $wgOut;
                            if( count( $wgOut->mCategoryLinks ) > 0 ) { ?>
                               <h3 class="heading">Page Categories</h3>
                               <ul>
                                  <?php echo $this->getCategories(); ?>
                               </ul> <?php
                            }
                          ?>
                        <!-- Begin Sidebar Page Toolbox -->
                          <?php
                             global $wgUser;
                             if( $wgUser->isLoggedIn()) { 
                                ?>
                                 <h3>Page Toolbox</h3> 
                                 <ul class="disc"> <!-- Nest these to match the others, which are autogenerated. -->
                                 <ul >
                                    <?php
                                    if( $this->data['notspecialpage'] ) {
                                       ?>
                                       <li id="t-whatlinkshere">
                                          <a href="<?php
                                                   echo htmlspecialchars($this->data['nav_urls']['whatlinkshere']['href'])
                                                   ?>"
                                          <?php echo $skin->tooltipAndAccesskeyAttribs('t-whatlinkshere') ?>>
                                          <?php $this->msg('whatlinkshere') ?></a>
                                       </li>
                                       <?php
                                       if( $this->data['nav_urls']['recentchangeslinked'] ) { 
                                          ?>
                                          <li id="t-recentchangeslinked"><a href="<?php
                                             echo htmlspecialchars($this->data['nav_urls']['recentchangeslinked']['href'])
                                             ?>"
                                             <?php echo $skin->tooltipAndAccesskeyAttribs('t-recentchangeslinked') ?>>
                                             <?php $this->msg('recentchangeslinked') ?></a>
                                          </li>
                                          <?php
                                       }
                                    }
                                    if( isset( $this->data['nav_urls']['trackbacklink'] ) ) { 
                                       ?>
                                       <li id="t-trackbacklink">
                                          <a href="<?php
                                             echo htmlspecialchars($this->data['nav_urls']['trackbacklink']['href'])
                                             ?>"
                                          <?php echo $skin->tooltipAndAccesskeyAttribs('t-trackbacklink') ?>>
                                          <?php $this->msg('trackbacklink') ?></a>
                                       </li>
                                       <?php
                                    }
                                    if( $this->data['feeds'] ) { 
                                       ?>
                                       <li id="feedlinks">
                                          <?php foreach($this->data['feeds'] as $key => $feed) {
                                             ?>
                                             <span id="feed-<?php echo Sanitizer::escapeId($key) ?>"><a href="<?php
                                 echo htmlspecialchars($feed['href']) ?>"
                                                <?php echo $skin->tooltipAndAccesskeyAttribs('feed-'.$key) ?>>
                                                <?php echo htmlspecialchars($feed['text'])?></a>&nbsp;
                                             </span>
                                          <?php
                                          } 
                                          ?>
                                       </li>
                                       <?php
                                    }
                                 
                                    foreach( array( 'contributions', 'blockip', 'emailuser', 'upload', 'specialpages' ) as $special ) {
                                 
                                       if( $this->data['nav_urls'][$special] ) {
                                          ?>
                                          <li id="t-<?php echo $special ?>">
                                          <a href="<?php echo htmlspecialchars($this->data['nav_urls'][$special]['href'])
                                             ?>"
                                             <?php echo $skin->tooltipAndAccesskeyAttribs('t-'.$special) ?>>
                                             <?php $this->msg($special) ?></a>
                                          </li>
                                          <?php
                                       }
                                    }
                                 
                                    if( !empty( $this->data['nav_urls']['print']['href'] ) ) { 
                                       ?>
                                       <li id="t-print">
                                       <a href="<?php echo htmlspecialchars($this->data['nav_urls']['print']['href'])
                                          ?>"<?php echo $skin->tooltipAndAccesskeyAttribs('t-print') ?>>
                                          <?php $this->msg('printableversion') ?></a>
                                       </li>
                                       <?php
                                    }
                                 
                                    if( !empty( $this->data['nav_urls']['permalink']['href'] ) ) { 
                                       ?>
                                       <li id="t-permalink"><a href="<?php echo htmlspecialchars($this->data['nav_urls']['permalink']['href'])
                                          ?>"<?php echo $skin->tooltipAndAccesskeyAttribs('t-permalink') ?>>
                                          <?php $this->msg('permalink') ?></a>
                                       </li>
                                       <?php
                                    } elseif( $this->data['nav_urls']['permalink']['href'] === '' ) { 
                                       ?>
                                       <li id="t-ispermalink"<?php echo $skin->tooltip('t-ispermalink') ?>>
                                       <?php $this->msg('permalink') ?>
                                       </li>
                                       <?php
                                    }
                                    wfRunHooks( 'SkinTemplateToolboxEnd', array( &$this ) );
                                    ?>
                                       <?php if( $this->data['undelete'] ) {echo "<li>"; $this->html('undelete'); echo "</li>"; } ?>

                                 </ul>                         
                                 </ul>
                                 <?php
                             } ?>
                              <!-- End Page Toolbox -->

                         <div id="socialMediaIcons"> <!-- Again, for the styling. Not actually sure what to put here. -->
                            <?php /* Powered by Mediawiki. */
                                  $this->html("poweredbyico"); ?>
                         </div>
                         <!-- More general 'social' button, called "AddThis" -->
                         <!-- AddThis Button BEGIN -->
                         <div id="social" style="text-align:left;margin-left:0px;margin-top:0px;">
                            <a href="http://www.addthis.com/bookmark.php?v=250" 
                               onmouseover="return addthis_open(this, '', '[URL]', '[TITLE]')" 
                               onmouseout="addthis_close()" onclick="return addthis_sendto()">
                               <img src="//s7.addthis.com/static/btn/lg-share-en.gif" 
                                    width="125" height="16" alt="Bookmark and Share" style="border:0" />
                            </a>
                            <script type="text/javascript" 
                                    src="//s7.addthis.com/js/250/addthis_widget.js?pub=xa-4a32489d7efa03cb">
                            </script>
                        </div>
                        <!-- AddThis Button END --> 
                     </div>

                     <!-- End left bar stuff -->
                  </div>
               </div>
               <!-- end: #col1 -->

               <!-- begin: #col3 static column -->
               <div id="col3">
                  <div id="col3_content" class="clearfix">
                     <a id="content" name="content"></a>
                     <!-- skiplink anchor: Content -->
                     <div id="toptabs-container" class="top-tabs">
                        <?php /* This is empty in content pages, so
                               * I'm going to put the site notice here instead
                               * See: http://www.mediawiki.org/wiki/Manual:Interface/Sitenotice
                               */
                         ?>
                        <?php if( $this->data['sitenotice'] ) { ?><div id="siteNotice"><?php $this->html('sitenotice') ?></div><?php } ?>

                        <?php /* And also the new talk message notification. */ ?>
                        <?php if( $this->data['newtalk'] ) { ?><div class="usermessage"><?php $this->html('newtalk') ?></div><?php } ?>
                     </div>
                     <!--Start center content area-->      
                     <!-- start main slot where content gets rendered--> 
                     <div class="newsitem">
                        <div style="position:relative;">  
                           <?php /* There's already an edit link over on the left, but this is for ease of use. */
                           if( $wgUser->isLoggedIn() && isset($this->data['content_actions']['edit']) ) 
                           { ?> 
                           <!-- To match the section edits, currently. -->
                              <div class="editpage-side" style="position:absolute; right:0px; bottom:0px; font-style: italic; font-weight: bold; font-size: 10pt;">
                              <?php
                                 echo "[<a href=\"" . htmlspecialchars($this->data['content_actions']['edit']['href']) .
                                      "\" title=\"Edit this page\">" . htmlspecialchars($this->data['content_actions']['edit']['text']) .
                                      "</a>]";
                              ?>
                              </div> 
   			 <?php } ?>
			   <h2 class="heading newsitemheading">
			      <?php $this->html('title'); ?>
			   </h2>
                        </div>
                        <div class="newsiteminfo">
                           <p><strong class="datepublished-article"><?php $this->html('subtitle') ?></strong></p>
                        </div>
                        
                        <!-- This is style info for the main content region. It's here so that it can be easily modified by PHP. -->
                        <!-- In the long run, it might be better to make a logged in and not logged in stylesheet separately. -->
                        <!-- (And a separate printing one, I guess?) -->
                        <style>

                           /* Heading styles */ 
                           #mw-content-text h1 { width: 100%; border-bottom: 1px solid #E0E0E0; }
                           #mw-content-text h2 { width: 100%; border-bottom: 1px solid #E0E0E0; }
                           h1 span.mw-headline { font-size: 13pt; font-weight: bold; } 
                           h2 span.mw-headline { font-size: 12pt; font-weight: bold; } 
                           h3 span.mw-headline { font-size: 11pt; font-weight: bold; } 
                           h5 span.mw-headline { font-size: 11pt; font-weight: bold; font-style: italic; } 
                           h4 span.mw-headline { font-size: 11pt; font-weight: bold; font-style: italic; color: #999999 } 
                           h6 span.mw-headline { font-size: 10pt; font-style: italic; color: #999999; } 
                           a.new { color: #AAAAFF; text-decoration: none; border-bottom: 1px #AAAAFF dotted; } /* Links to pages that don't yet exist. */
                           

                           /* Diff page styling */
                           *.diff { background-color: #F0F0F0; }
                           *.diff-addedline { background-color: #CDFAC8; color: #BF0000; }
                           *.diff-deletedline { background-color: #FFD4D4; }
                           *.diff-context { background-color: #E0E0E0; }
                           #mw-diff-otitle4 { text-align: center; } /* Older revision link */
                           #mw-diff-ntitle4 { text-align: center; } /* Newer revision link */
                           h2.diff-currentversion-title { font-size: 12pt; font-weight: bold; }

                           /* Notice boxes */
                           /*
                           .noticebox-info          { border: 1px solid black; background-color: #EEEEFF; width:75%; }
                           .noticebox-outage        { border: 1px solid black; background-color: #FFEEEE; width:75%; }
                           .noticebox-warning       { border: 1px solid black; background-color: #FFE0F0; width:75%; }
                           .noticebox-info-small    { border: 1px solid black; background-color: #EEEEFF; width:300px; float: right; }
                           .noticebox-outage-small  { border: 1px solid black; background-color: #FFEEEE; width:300px; float: right; }
                           .noticebox-warning-small { border: 1px solid black; background-color: #FFE0F0; width:300px; float: right; }
                           */
                           /* Contents box */
			   /* #toc { background-color: #eeeeff; padding: 0.1em; margin: 1em; border: 1px solid black; } */
			   #toc { background-color: #f7f7f7; padding: 0.1em; margin: 1em 0em 1em 0em; border: 1px solid #cccccc; } 
                           #toctitle h2 { font-size:12pt; font-weight: bold; }
                           #toc ul { margin: 0 0 0 0; }
                           #lastmod { font-style: italic; text-align: right; list-style-type: none; }
                           #viewcount { font-style: italic; text-align: right; list-style-type: none; }
                           <?php
                              global $wgUser;
                              if(  !StubObject::isRealObject( $wgUser )  
                                  || !$wgUser->isLoggedIn()) 
                              { 
                              /* Section for not logged in users */?>
                              /* Note! This does not remove the link from the page, 
                               *  so non-logged in users should be set to not be able 
                               *  to edit pages regardless. 
                               * Removing the edit links is just for cleanliness.
                               */
                                 span.editsection { visible=none; }
                              <?php
                              } else {
                              /* Section for logged in users */?>
                                 span.editsection { float: right; font-style: italic; font-size: 10pt; }
                              <?php
                              }
                            ?>

                        </style>

                        <div class="mwcontent">
                           
                           <!-- Actual content here. -->
                           <?php $this->html('bodycontent') ?>
                           <!-- end main slot where content gets rendered--> 
                        
                        </div>
                        <!--End center content area-->
                        <!-- start content footer information -->
                        <div class="contentfooter">
                           <?php
                              global $wgUser;
                              if(  !StubObject::isRealObject( $wgUser )  
                                  || !$wgUser->isLoggedIn()) 
                              { 
                                 $footerlinks = array(
                                    'lastmod', 'viewcount', 'numberofwatchingusers',
                                 );
                              } else {
                                 $footerlinks = array( 'lastmod' );
                              }
                           ?>
                           <ul id="mwfooterlinks">
                              <?php 
                                 foreach ( $footerlinks as $aLink ) {
                                    if ( isset( $this->data[$aLink] ) && $this->data[$aLink] ) {
                                       ?>
                                       <li id="<?php echo $aLink ?>"><?php $this->html( $aLink ) ?></li>
                                       <?php 
                                    }
                                 }
                               ?>
                           </ul>
                        </div>
                        <!-- end content footer information -->

                     </div>
                     <div id="ie_clearing">&nbsp;</div>
                     <!-- End: IE Column Clearing -->
                  </div>
                  <!-- end: #col3 -->
               </div>
            </div>
            <!-- end: #main -->
            <!-- begin: #footer -->
            <!-- =============== FOOTER =============== -->


            <div id="footer"> 
               <ul>
                  <li><a href="http://www.ucl.ac.uk/disclaimer">Disclaimer</a></li>
                  <li><a href="http://www.ucl.ac.uk/foi">Freedom of Information</a></li>
                  <li><a href="http://www.ucl.ac.uk/accessibility">Accessibility</a></li>
                  <li><a href="http://www.ucl.ac.uk/privacy">Privacy</a></li>
                  <li><a href="http://www.ucl.ac.uk/cookies">Cookies</a></li>
                  <li><a href="http://www.ucl.ac.uk/advanced-search">Advanced Search</a></li>
                  <li><a href="http://www.ucl.ac.uk/contact-list/">Contact Us</a></li>
              </ul>
  
              <div>
                 <address class="vcard">
                    <span class="adr">
                       University College London - Gower Street - London - WC1E 6BT
                    </span>
                    <span class="tel">
                       <span class="type">Tel</span>:
                       <span class="value">+44 (0)20 7679 2000</span>
                    </span>
                 </address>
                 <p>&#169; UCL 1999&#8211;2012</p>
              </div>
              <div id="editbar">
                 <span id="silvaedit">
                    <?php /* Instead of an edit Silva link here, there's a login link. */ ?>
                    <a <?php /* Login, or logout */
                     global $wgUser;
                     if(  !StubObject::isRealObject( $wgUser )
                       || !$wgUser->isLoggedIn()) {
                         if ( array_key_exists('anonlogin', $this->data['personal_urls']) ) {
                            echo "href=\"" . $this->data['personal_urls']['anonlogin']['href'] . "\" title=\"" . 
                                 $this->data['personal_urls']['anonlogin']['text'] . "\"";
                         } elseif ( array_key_exists('login', $this->data['personal_urls']) ) {
                            echo "href=\"" . $this->data['personal_urls']['login']['href'] . "\" title=\"" . 
                              $this->data['personal_urls']['login']['text'] . "\"";
                         } else {
                            echo "> Erk, something is broken.";
                            echo "Contents of 'personal_urls': ";
                            foreach ($this->data['personal_urls'] as $k=>$v) {
                              echo "$k --> $v ;";
                            }  
                            echo "<br />Does anonlogin exist? ";
                            if (array_key_exists('anonlogin', $this->data['personal_urls'])) {
                              echo "Y";
                            } else {
                              echo "N";
                            }
                         }
                     } else {
                        echo "href=\"" . $this->data['personal_urls']['logout']['href'] . "\" title=\"" . 
                          $this->data['personal_urls']['logout']['text'] . "\"";
                        echo " type=\"logged_in\"";
                     }
                     ?>>
                    <img
                       id="edit_button"
                       src="//www.ucl.ac.uk/news/++resource++images/edit.gif"
                       alt="edit page" width="10" height="10" /></a>
                 </span>
              </div>

           </div>

 
           <!-- end #footer -->
        </div>
        <!-- Graphic Border - Begin Part 2 -->
        <div id="border-bottom">
           <div id="edge-bl"> </div>
           <div id="edge-br"> </div>
        </div>
        <!-- Graphic Border - End Part 2 -->
     </div>
      



<!-- scripts and debugging information -->
<?php // $this->html('bottomscripts'); /* JS call to runBodyOnloadHook */ ?>
<?php // $this->html('reporttime') ?>

<?php if ( $this->data['debug'] ): ?>
<!-- Debug output:
<?php  $this->text( 'debug' ); ?>
 
-->
<?php endif; ?>

</body>
</html>
<?php
        wfRestoreWarnings();
   } // end of execute() method

   
   /* hijack category functions to create a proper list */

   function getCategories_debug() {
      global $wgOut;
      $s = "";
      foreach ($wgOut->mCategoryLinks as $key=>$value) {
         $s .= "$key -> $value \n";
         foreach ($value as $key1=>$value1) {
            $s .= "  $key1 -> $value1 \n";
         }
      }
      return $s;
   }

   function getCategories() {
      $catlinks = $this->getCategoryLinks();
      if( !empty( $catlinks ) ) {
         return "<ul id='catlinks' class='disc'>{$catlinks}</ul>";
      } else {
         return "";
      }
   }

   function makeLinkObj( $href, $text ) {
      return "<a href=\"{$href}\">{$text}</a>";
   }

   function getCategoryLinks() {
      global $wgOut, $wgUseCategoryBrowser;
      global $wgContLang;

      if( count( $wgOut->mCategoryLinks ) == 0 ) {
         return '';
      }

      # separator
      $sep = '';

      // use Unicode bidi embedding override characters,
      // to make sure links don't smash each other up in ugly ways
      $dir = $wgContLang->isRTL() ? 'rtl' : 'ltr';
      $embed = "<li dir='$dir'>";
      $pop = '</li>';
      $t = $embed;
      $t .= implode( "{$pop} {$sep} {$embed}", $wgOut->mCategoryLinks['normal'] ) . $pop;
      //$t .= gettype ( $wgOut->mCategoryLinks ) . $pop;
      //$msg = wfMsgExt( 'pagecategories', array( 'parsemag', 'escape' ), count( $wgOut->mCategoryLinks ) );
      $msg = "";
      $s = $this->makeLinkObj( Title::newFromText( wfMsgForContent( 'pagecategorieslink' ) ), $msg )
            . $t;
      //$s = $t; //Ugly hack to just remove the Special:Categories link from the top.

      # optional 'dmoz-like' category browser - will be shown under the list
      # of categories an article belongs to
      if( $wgUseCategoryBrowser ) {
         $s .= '<br /><hr />';

         # get a big array of the parents tree
         $parenttree = $this->getTitle()->getParentCategoryTree();
         # Skin object passed by reference because it can not be
         # accessed under the method subfunction drawCategoryBrowser
         $tempout = explode( "\n", $this->drawCategoryBrowser( $parenttree, $this ) );
         # clean out bogus first entry and sort them
         unset( $tempout[0] );
         asort( $tempout );
         # output one per line
         $s .= implode( "<br />\n", $tempout );
      }

      return $s;
   }



} // end of class
?>

