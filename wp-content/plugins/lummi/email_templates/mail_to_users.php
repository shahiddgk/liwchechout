<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title></title>
    <style>
		#emailContainer{
			width: 100%;
			min-width: 30%;
		}
	</style>
</head>
<body>
    <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
        <tr>
            <td align="center" valign="top">
                <table border="0" cellpadding="20" cellspacing="0" width="100%" id="emailContainer">
                    <tr>
                        <td align="left" valign="top">
							<hr>
                            <p style="font-size: 1.2em;">From: <?php echo $attributes['from']; ?></p>
							<p style="font-size: 1.2em;">Subject: <?php echo $attributes['subject']; ?></p>
							<hr>
                        </td>
                    </tr>
					<tr>
                        <td align="left" valign="top" style="font-size: 1.2em;">
                           <?php echo $attributes['lw_content_message'];?>
						</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>