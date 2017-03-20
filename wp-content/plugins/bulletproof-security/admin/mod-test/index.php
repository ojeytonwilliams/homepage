<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>mod_rewrite, mod_authz_core, mod_authz_host &amp; mod_access_compat Module Testing</title>
<meta name="robots" content="noindex, nofollow" />
</head>

<body>
<style type="text/css">
<!--
body {background-color:#333333;}
#bps-xtf-container {background-color:#fafafa;}
h1 {padding:0px 0px 0px 10px;}
/* p {font-weight:bold;padding:0px 0px 0px 10px;} */
table td, table th {background-color:#fff;}
pre {background:#fff url(pre_bg.png) top left repeat;color:#000;display:block;font-family:"Courier New", Courier, monospace;font-size:12px;width:100%;}
-->
</style>

<h2 style="color:#fff;">Apache Modules: mod_rewrite, mod_authz_core, mod_authz_host &amp; mod_access_compat (directives: Order, Allow and Deny) testing</h2>

<table class="Mod-Directive-Testing" width="100%" border="1">
<thead>
	<tr>
	<th scope="col" style="width:2.3%;">#</th>
	<th scope="col" style="width:5%;">Images</th>
	<th scope="col" style="width:35%;">htaccess Code</th>
	<th scope="col" style="width:2.3%;">#</th>
	<th scope="col" style="width:5%;">Images</th>
	<th scope="col" style="width:30%;">htaccess Code</th>
	</tr>
</thead>

<tbody>
	<tr>
	<th scope="row" style="font-size:30px;">1</th>
	<td style="text-align:center;background-color:#333333;"><img src="mod_access_compat-od-allowed.png" alt="" title="" /></td>
	<td>
<pre>
# 1 mod_access_compat Order Directive Allow from all
&lt;FilesMatch &quot;^(mod_access_compat-od-allowed\.png)$&quot;&gt;
	&lt;IfModule mod_access_compat.c&gt;
		Order Allow,Deny
		Allow from all
	&lt;/IfModule&gt;
&lt;/FilesMatch&gt;
</pre>
	</td>
    <td style="text-align:center;"><span style="font-size:30px;font-weight:bold;">2</span></td>
    <td style="text-align:center;background-color:#333333;"><img src="mod_access_compat-od-denied.png" alt="" title="" /></td>
    <td>
<pre>
# 2 mod_access_compat Order Directive Deny from all
&lt;FilesMatch &quot;^(mod_access_compat-od-denied\.png)$&quot;&gt;
	&lt;IfModule mod_access_compat.c&gt;
		Order Allow,Deny
		Deny from all
	&lt;/IfModule&gt;
&lt;/FilesMatch&gt;
</pre>
	</td>
	</tr>
	<tr>
	<th scope="row" style="font-size:30px;">3</th>
    <td style="text-align:center;background-color:#333333;"><img src="mod_authz_core-denied.png" alt="" title="" /></td>
	<td>
<pre>
# 3 mod_authz_core Require all denied Conditional	       
&lt;FilesMatch &quot;^(mod_authz_core-denied\.png)$&quot;&gt;
	&lt;IfModule mod_authz_core.c&gt;
		Require all denied
	&lt;/IfModule&gt;
&lt;/FilesMatch&gt;
</pre>
    </td>
    <td style="text-align:center;"><span style="font-size:30px;font-weight:bold;">4</span></td>
    <td style="text-align:center;background-color:#333333;"><img src="mod_authz_core-od-cond-denied.png" alt="" title="" /></td>
    <td>
<pre>
# 4 mod_authz_core|mod_access_compat Order Directive Denied Conditional
&lt;FilesMatch &quot;^(mod_authz_core-od-cond-denied\.png)$&quot;&gt;
	&lt;IfModule mod_authz_core.c&gt;
		Order Allow,Deny
		Deny from all
	&lt;/IfModule&gt;
&lt;/FilesMatch&gt;
</pre>
    </td>
	</tr>
	<tr>
	<th scope="row" style="font-size:30px;">5</th>
    <td style="text-align:center;background-color:#333333;"><img src="mod_authz_host-require-ip.png" alt="" title="" /></td>
	<td>
<pre>
# 5 mod_authz_host Require ip 127.9.9.1 Conditional	       
&lt;FilesMatch &quot;^(mod_authz_host-require-ip\.png)$&quot;&gt;
	&lt;IfModule mod_authz_host.c&gt;
		Require ip 127.9.9.1
	&lt;/IfModule&gt;
&lt;/FilesMatch&gt;
</pre>
    </td>
    <td style="text-align:center;"><span style="font-size:30px;font-weight:bold;">6</span></td>
    <td style="text-align:center;background-color:#333333;"><img src="mod_authz_host-od-cond-denied.png" alt="" title="" /></td>
    <td>
<pre>
# 6 mod_authz_host|mod_access_compat Order Directive Denied Conditional	       
&lt;FilesMatch &quot;^(mod_authz_host-od-cond-denied\.png)$&quot;&gt;
	&lt;IfModule mod_authz_host.c&gt;
		Order Allow,Deny
		Deny from all
	&lt;/IfModule&gt;
&lt;/FilesMatch&gt;
</pre>
    </td>
	</tr>
</tbody>
</table>

<h3 style="color:#fff;">Apache Module &amp; Directive Test Results Explanation</h3>
<p style="color:#fff;">This test will tell you if your server has the mod_authz_core, mod_authz_host and mod_access_compat Modules loaded and whether or not the mod_access_compat Module directives (deprecated in Apache 2.4): <strong>"Order, Allow, Deny"</strong> can be used/will work on your server or can be used in combination with the mod_authz_core and mod_authz_host Modules (backward compatibility). The new Apache 2.4 Modules for Access Control are: mod_authz_host and mod_authz_core. <br /><strong>Note:</strong> The <strong>"Require ip"</strong> directive can be used in <strong>BOTH</strong> the mod_authz_host and mod_authz_core Modules htaccess code.</p>

<table class="Mod-Directive-Testing-Description" width="100%" border="1">
<thead>
	<tr>
	<th scope="col" style="width:1.3%;">#</th>
	<th scope="col" style="width:22.9%;">Explanation|Description</th>
	<th scope="col" style="width:1.3%;">#</th>
	<th scope="col" style="width:23%;">Explanation|Description</th>
	</tr>
</thead>

<tbody>
	<tr>
	<th scope="row" style="font-size:30px;">1</th>
	<td style="padding:5px;">If you see an image displayed then your Apache server has the mod_access_compat Module loaded and allows the mod_access_compat <strong>"Order, Deny, Allow"</strong> directives to be used in htaccess files. <strong>"Allow from all"</strong> means to display the mod_access_compat-od-allowed.png image file to everyone. Or your server is ignoring/not processing the IfModule condition. See Test #7 and check the System Info page.</td>
    <td style="text-align:center;"><span style="font-size:30px;font-weight:bold;">2</span></td>
    <td style="padding:5px;">If you do <strong>NOT</strong> see an image displayed then your Apache server has the mod_access_compat Module loaded and allows the mod_access_compat <strong>"Order, Deny, Allow"</strong> directives to be used in htaccess files. <strong>"Deny from all"</strong> means do <strong>NOT</strong> display the mod_access_compat-od-denied.png image file to anyone. Or if your server is ignoring/not processing the IfModule condition then an image will be displayed. See Test #8 and check the System Info page.</td>
	</tr>
	<tr>
	<th scope="row" style="font-size:30px;">3</th>
	<td style="padding:5px;">The IfModule condition is checking if the mod_authz_core Module <strong>IS</strong> loaded on your Apache server. If the Module is loaded then you will <strong>NOT</strong> see the mod_authz_core-denied.png image file displayed to you. <strong>"Require all denied"</strong> means do <strong>NOT</strong> display the image file to anyone. Or if your server is ignoring/not processing the IfModule condition then an image will be displayed. See Test #10 and check the System Info page.</td>
    <td style="text-align:center;"><span style="font-size:30px;font-weight:bold;">4</span></td>
    <td style="padding:5px;">The IfModule condition is checking if the mod_authz_core Module <strong>IS</strong> loaded on your Apache server. If the Module is loaded and your server allows using the mod_access_compat <strong>"Order, Deny, Allow"</strong> directives <strong>WITH</strong> the mod_authz_core IfModule condition then the mod_authz_core-od-cond-denied.png image file should <strong>NOT</strong> be displayed. This means that your server has backward compatibility, which is using/allowing/loading both of these Modules and allowing the <strong>"Order, Deny, Allow"</strong> directives to be used in htaccess files. If the mod_authz_core Module is <strong>NOT</strong> loaded and/or your server does NOT allow using the <strong>"Order, Deny, Allow"</strong> directives then your server will not process this code and the image file <strong>WILL</strong> be displayed to you. Check the System Info page.</td>
	</tr>
	<tr>
	<th scope="row" style="font-size:30px;">5</th>
	<td style="padding:5px;">The IfModule condition is checking if the mod_authz_host Module <strong>IS</strong> loaded on your Apache server. If the Module is loaded then you will <strong>NOT</strong> see the mod_authz_host-require-ip.png image file displayed to you. <strong>"Require ip 127.9.9.1"</strong> means <strong>ONLY</strong> display the image file if your IP address is 127.9.9.1. The 127.9.9.1 IP address is intentionally a bogus IP address and is <strong>NOT</strong> your IP address. If the mod_authz_host Module is <strong>NOT</strong> loaded then your server will <strong>NOT</strong> process this code and the image file <strong>WILL</strong> be displayed to you.</td>
    <td style="text-align:center;"><span style="font-size:30px;font-weight:bold;">6</span></td>
    <td style="padding:5px;">The IfModule condition is checking if the mod_authz_host Module <strong>IS</strong> loaded on your Apache server. If the Module is loaded and your server allows using the mod_access_compat <strong>"Order, Deny, Allow"</strong> directives <strong>WITH</strong> the mod_authz_host IfModule condition then the mod_authz_host-od-cond-denied.png image file should <strong>NOT</strong> be displayed. This means that your server has backward compatibility, which is using/allowing/loading both of these Modules and allowing the <strong>"Order, Deny, Allow"</strong> directives to be used in htaccess files. If the mod_authz_host Module is <strong>NOT</strong> loaded and/or your server does <strong>NOT</strong> allow using the <strong>"Order, Deny, Allow"</strong> directives then your server will not process this code and the image file <strong>WILL</strong> be displayed to you.</td>
	</tr>
</tbody>
</table>

<h3 style="color:#fff;">Additional Testing for Web Hosts that ignore/do not allow/do not process IfModule conditions:</h3>

<table class="Mod-Directive-Testing-no-ifmodule" width="100%" border="1">
<thead>
	<tr>
	<th scope="col" style="width:2.3%;">#</th>
	<th scope="col" style="width:5%;">Images</th>
	<th scope="col" style="width:27%;">htaccess Code</th>
	<th scope="col" style="width:2.2%;">#</th>
	<th scope="col" style="width:5%;">Images</th>
	<th scope="col" style="width:30%;">htaccess Code</th>
	</tr>
</thead>

<tbody>
	<tr>
	<th scope="row" style="font-size:30px;">7</th>
	<td style="text-align:center;background-color:#333333;"><img src="mod_access_compat-od-nc-allowed.png" alt="" title="" /></td>
	<td>
<pre>
# 7 mod_access_compat: No IfModule Condition Order Directive Allow from all
&lt;FilesMatch &quot;^(mod_access_compat-od-nc-allowed\.png)$&quot;&gt;
Order Allow,Deny
Allow from all
&lt;/FilesMatch&gt;
</pre>
	</td>
    <td style="text-align:center;"><span style="font-size:30px;font-weight:bold;">8</span></td>
    <td style="text-align:center;background-color:#333333;"><img src="mod_access_compat-od-nc-denied.png" alt="" title="" /></td>
    <td>
<pre>
# 8 mod_access_compat: No IfModule Condition Order Directive Deny from all
&lt;FilesMatch &quot;^(mod_access_compat-od-nc-denied\.png)$&quot;&gt;
Order Allow,Deny
Deny from all
&lt;/FilesMatch&gt;
</pre>
	</td>
	</tr>
	<tr>
	<th scope="row" style="font-size:30px;">9</th>
	<td style="text-align:center;background-color:#333333;"><img src="mod_rewrite-cond.png" alt="" title="" /></td>
	<td>
<pre>
# 9 mod_rewrite: No IfModule Condition
RewriteCond %{HTTPS} !=on
RewriteCond %{REQUEST_URI} ^.*/mod-test/mod_rewrite-cond\.png$ [NC]
RewriteRule ^(.*)$ http://%{HTTP_HOST}.../mod_rewrite-nc.png [R=302]
RewriteCond %{HTTPS} =on
RewriteCond %{REQUEST_URI} ^.*/mod-test/mod_rewrite-cond\.png$ [NC]
RewriteRule ^(.*)$ https://%{HTTP_HOST}.../mod_rewrite-nc.png [R=302]
</pre>
	</td>
    <td style="text-align:center;"><span style="font-size:30px;font-weight:bold;">10</span></td>
    <td style="text-align:center;background-color:#333333;"><img src="mod_authz_core-nc-denied.png" alt="" title="" /></td>
    <td>
<pre>
# 10 mod_authz_core: No IfModule Condition Require all denied
&lt;FilesMatch &quot;^(mod_authz_core-nc-denied\.png)$&quot;&gt;
Require all denied
&lt;/FilesMatch&gt;
</pre>
	</td>
	</tr>
	<tr>
	<th scope="row" style="font-size:30px;">11</th>
	<td style="text-align:center;background-color:#333333;"><img src="mod_authz_host-nc-require-host.png" alt="" title="" /></td>
	<td>
<pre>
# 11 mod_authz_host: No IfModule Condition Require host
&lt;FilesMatch &quot;^(mod_authz_host-nc-require-host\.png)$&quot;&gt;
Require host example.com
&lt;/FilesMatch&gt;
</pre>
	</td>
    <td style="text-align:center;"><span style="font-size:30px;font-weight:bold;">12</span></td>
    <td style="text-align:center;background-color:#333333;"><img src="mod_access_compat-od-nc-allowed.png" alt="" title="" /></td>
    <td>
<pre>
# 12 Unused: No Tests Performed
</pre>
	</td>
	</tr>
</tbody>
</table>

<h3 style="color:#fff;">Apache No IfModule Condition & Directive Test Results Explanation:</h3>
<p style="color:#fff;">Some Web Hosts ignore/do not allow/do not process all IfModule conditions and the test results for tests 1-6 above will NOT be accurate. Tests 7-11 check mod_access_compat, mod_rewrite, mod_authz_core and mod_authz_host directives without any IfModule conditions. The mod_access_compat directives <strong>"Order, Deny, Allow"</strong> should work on every single Web Host at this present time. The Apache transition from mod_access_compat to the new mod_authz_core and mod_authz_host Modules will probably result in various issues/problems on some Web Hosts during that transitional period. BPS checks your current loaded Modules and directive htaccess code compatiblity and creates htaccess code that works specifically on your particular server/website/host. If things change in the future with your Web Host or you move to another host or server, BPS will check that you htaccess code is correct for your particular server/website/host and display a message to run the Setup Wizard again if necessary, which will create new htaccess code that works specifically for your particular server/website/host.</p>

<table class="Mod-Directive-Testing-Description" width="100%" border="1">
<thead>
	<tr>
	<th scope="col" style="width:1.3%;">#</th>
	<th scope="col" style="width:22.9%;">Explanation|Description</th>
	<th scope="col" style="width:1%;">#</th>
	<th scope="col" style="width:23%;">Explanation|Description</th>
	</tr>
</thead>

<tbody>
	<tr>
	<th scope="row" style="font-size:30px;">7</th>
	<td style="padding:5px;">If you see an image displayed then your Apache server has the mod_access_compat Module loaded and allows the mod_access_compat <strong>"Order, Deny, Allow"</strong> directives to be used in htaccess files. <strong>"Allow from all"</strong> means to display the mod_access_compat-od-nc-allowed.png image file to everyone. Or your server is not processing the mod_access_compat <strong>"Order, Deny, Allow"</strong> directives and you will see the image. See the System Info page.</td>
    <td style="text-align:center;"><span style="font-size:30px;font-weight:bold;">8</span></td>
    <td style="padding:5px;">If you do <strong>NOT</strong> see an image displayed then your Apache server has the mod_access_compat Module loaded and allows the mod_access_compat <strong>"Order, Deny, Allow"</strong> directives to be used in htaccess files. <strong>"Deny from all"</strong> means do <strong>NOT</strong> display the mod_access_compat-od-nc-denied.png image file to anyone. Or your server is not processing the mod_access_compat <strong>"Order, Deny, Allow"</strong> directives and you will see the image. See the System Info page.</td>
	</tr>
	<tr>
	<th scope="row" style="font-size:30px;">9</th>
	<td style="padding:5px;">Note: The path has been shortened in the code example with ... for visual formatting display reasons.
If you see an image displayed <strong>without</strong> a white #9 displayed in the bottom right corner then your Apache server has the mod_rewrite Module loaded. The image that is being displayed is the mod_rewrite-nc.png image file. If mod_rewrite is <strong>NOT</strong> loaded then a 500 error will occur and you will see the mod_rewrite-cond.png image file displayed <strong>with</strong> a white #9 displayed in the bottom right corner. Or you will see the mod_rewrite-cond.png image file displayed if you are not using either a WordPress default or BPS root htaccess file. See the System Info page.</td>
    <td style="text-align:center;"><span style="font-size:30px;font-weight:bold;">10</span></td>
    <td style="padding:5px;">If you do <strong>NOT</strong> see an image displayed then your Apache server has the mod_authz_core Module loaded on your server. If the Module is loaded then you will <strong>NOT</strong> see the mod_authz_core-nc-denied.png image file displayed to you. <strong>"Require all denied"</strong> means do <strong>NOT</strong> display the mod_authz_core-nc-denied.png image file to anyone. Or your server is not processing the <strong>"Require all denied"</strong> directive and a 500 error will occur and you will not see the image. See the System Info page.</td>
	</tr>
	<tr>
	<th scope="row" style="font-size:30px;">11</th>
	<td style="padding:5px;">If you do <strong>NOT</strong> see an image displayed then your Apache server has the mod_authz_host Module loaded on your server. If the Module is loaded then you will <strong>NOT</strong> see the mod_authz_host-nc-require-host.png image file displayed to you. <strong>"Require host example.com"</strong> means <strong>ONLY</strong> display the image file if the Hostname (your website) is example.com. The Hostname is intentionally a bogus Hostname and is <strong>NOT</strong> your Hostname (your website). Or your server is not processing the <strong>"Require host example.com"</strong> directive and a 500 error will occur and you will not see the image. See the System Info page.</td>
    <td style="text-align:center;"><span style="font-size:30px;font-weight:bold;">12</span></td>
    <td style="padding:5px;">Unused: No Tests Performed.</td>
	</tr>
</tbody>
</table>

</body>
</html>