<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0"
    xmlns:html="http://www.w3.org/TR/REC-html40"
    xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <title>Google Video Sitemap Feed With Multisite Support by Art Project Group</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style type="text/css">
	body {
		font-size: 0.8em;
		background-color: #eee;
		width: 75%;
		margin: 1em auto;
	}
	ul {
		list-style: none;
	}
	li {
		position: relative;
		display: inline-block;
		margin: 0.5em;
		width: 15%;
		vertical-align: top;
	}
	
	@media screen and (max-width: 540px) {
	li {
		width: 100%;
	}
	}
	
	@media screen and (min-width: 540px) and (max-width: 800px) {
	li {
		width: 46%;
	}
	}
	
	@media screen and (min-width: 800px) and (max-width: 1000px) {
	li {
		width: 30%;
	}
	}
	
	@media screen and (min-width: 1000px) and (max-width: 1200px) {
	li {
		width: 22.5%;
	}
	}
	a:hover {
		opacity: 0.5;
		-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";
		filter: alpha(opacity=50);
		-moz-opacity: 0.5;
		-khtml-opacity: 0.5;
	}
	img {
		width: 100%;
		height: auto;
	}
	p {
		position: absolute;
		top: 0px;
		width: 100%;
		color: #fff;
		background: rgba(2,2,2,0.5);
		font-style: italic;
		line-height: 1.1em;
		margin: 0px;
		padding: 0.5em;
		box-sizing: border-box;
	}
	</style>
    </head>
    <body>
    <ul>
      <xsl:for-each select="sitemap:urlset/sitemap:url">
        <xsl:variable name="u"> <xsl:value-of select="sitemap:loc"/> </xsl:variable>
        <xsl:variable name="t"> <xsl:value-of select="video:video/video:thumbnail_loc"/> </xsl:variable>
        <li><a href="{$u}" target="_blank">
          <div> <img src="{$t}" />
            <p> <xsl:value-of select="video:video/video:title"/> </p>
          </div>
          </a></li>
      </xsl:for-each>
    </ul>
    </body>
    </html>
  </xsl:template>
</xsl:stylesheet>
