<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title>Expand table rows with jQuery - jExpand plugin - JankoAtWarpSpeed demos</title>
    <style type="text/css">
        body { font-family:Arial, Helvetica, Sans-Serif; font-size:0.8em;}
        #report { border-collapse:collapse;}
        #report h4 { margin:0px; padding:0px;}
        #report img { float:right;}
        #report ul { margin:10px 0 10px 40px; padding:0px;}
        #report th { background:#7CB8E2 url(header_bkg.png) repeat-x scroll center left; color:#fff; padding:7px 15px; text-align:left;}
        #report td { background:#C7DDEE none repeat-x scroll center left; color:#000; padding:7px 15px; }
        #report tr.odd td { background:#fff url(row_bkg.png) repeat-x scroll center left; cursor:pointer; }
        #report div.arrow { background:transparent url(arrows.png) no-repeat scroll 0px -16px; width:16px; height:16px; display:block;}
        #report div.up { background-position:0px 0px;}
    </style>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript">  
        $(document).ready(function(){
            $("#report tr:odd").addClass("odd");
            $("#report tr:not(.odd)").hide();
            $("#report tr:first-child").show();
            
            $("#report tr.odd").click(function(){
                $(this).next("tr").toggle();
                $(this).find(".arrow").toggleClass("up");
            });
            //$("#report").jExpand();
        });
    </script>        
</head>

<?

function ExpandableDebugArray($Array,$Name)
  {
  echo "
    <table id='report'>

        <tr>
            <th>$Name</th>
            <th></th>
        </tr>";


   foreach ($Array as $Name => $Value)
	if(is_array($Value))
  ExpandableDebugArray($Value,$Name);
	 else
          echo "
        <tr>
            <td>$Value</td>
            <td><div class='arrow'></div></td>
        </tr>
        <tr>
            <td colspan='5'>
                <h4>Additional information</h4> 
            </td>
        </tr>";

echo "
    </table>";
};

?>
