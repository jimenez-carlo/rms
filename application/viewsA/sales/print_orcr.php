<html class="no-js">
<head>
    <title>OR CR Print</title>
    <link rel="shortcut icon" href="./../../images/favicon.ico"/>
    <!-- Bootstrap -->
    <link href="./../../bootstrap/css/bootstrap.min.css" rel="stylesheet" media="print">
    <link href="./../../assets/styles.css" rel="stylesheet" media="print">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="./../../vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    <style type="text/css" media="print">
    a {
        display:none;
    }
    </style>
</head>
<body onload="window.print();">
<!--  -->

<a href="orcr_print">Return to OR CR Search Page</a>
<?php
if (!empty($sales->files))
{
    foreach ($sales->files as $file)
    {
        $exp = explode('.', $file);
        $ext = array_pop($exp);
        $path = './../../rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine_no.'/'.$file;

        if ($ext == 'pdf')
        {
            print '
            <object data="'.$path.'" type="application/pdf" width="100%" height="100%" style="margin:1em; border:solid; float:left; width:100%; height:500px">
                <p><a href="'.$path.'">Download File</a>.</p>
            </object>';
        }
        else
        {
            print '<img src="'.$path.'" style="margin:1em; border:solid; float:left; width:100%; height:500px">';
        }
    }
}
else
{
    print "No attachments.";
}
?>
</body>
</html>