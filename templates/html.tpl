<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>New Form Submission</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<style type="text/css">
  body { height:100% !important; width:100% !important; margin:0; padding:0; }
  img, a img { border:0; outline:none; text-decoration:none; }
  table, td { border-collapse:collapse; }
</style>
</head>
<body style="margin:0;padding:0">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td bgcolor="#e8e8ea">&nbsp;</td>
</tr>
<tr>
<td bgcolor="#e8e8ea">
<table id="mainTable" align="center" width="600" border="0" cellspacing="0" cellpadding="0">
<tr>
<td align="center" bgcolor="#404044" style="background-color:#444455;padding:40px 0 30px 0;font-family:sans-serif;color:#b0b0b8">
<font face="sans-serif" color="#b0b0b8">
<h1>New Form Submission</h1>
<p>You have received a new message via your contact form.</p>
</font>
</td>
</tr>
<tr>
<td bgcolor="#ffffff" style="padding: 25px 35px 25px 35px;">
<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td align="right" valign="top">Name:&nbsp;</td><td valign="top">{{=it.name}}</td>
</tr>
<tr>
<td align="right" valign="top">E-Mail:&nbsp;</td><td valign="top">{{=it.email}}</td>
</tr>
<tr>
<td align="right" valign="top">Subject:&nbsp;</td><td valign="top">{{=it.subject}}</td>
</tr>
<tr>
<td align="right" valign="top">Date:&nbsp;</td><td valign="top">{{=it.date}}</td>
</tr>
<tr>
<td align="right" valign="top">User&nbsp;Agent:&nbsp;</td><td valign="top">{{=it.agent}}</td>
</tr>
<tr>
<td align="right" valign="top">IP&nbsp;Address:&nbsp;</td><td valign="top">{{=it.ip}}</td>
</tr>
</table>
{{=it.message}}
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td bgcolor="#e8e8ea">&nbsp;<br>&nbsp;<br>&nbsp;</td>
</tr>
</table>
</body>
</html>