<html class="no-js">
<head>
    <title>OR CR Print</title>
    <link rel="shortcut icon" href="./images/favicon.ico"/>
    <!-- Bootstrap -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="print">
    <link href="/assets/styles.css" rel="stylesheet" media="print">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="/vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
</head>
<body onload="window.print();">
<?php
if (!empty($sales->files))
{
    foreach ($sales->files as $file)
    {
        $exp = explode('.', $file);
        $ext = array_pop($exp);
        $path = base_url().'rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine_no.'/'.$file;

        print '<img src="'.$path.'" style="margin:1em; border:solid; float:left;">';
    }
}
else
{
    print "No attachments.";
}
?>
</body>
</html>
