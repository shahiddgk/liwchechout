<?php

require(LW_PLUGIN_DIR.'LwApp/FPDF/fpdf.php');
require('htmlparser.inc.php');

class PDF_HTML_Table extends FPDF
{
protected $B;
protected $I;
protected $U;
protected $HREF;

function __construct($orientation='P', $unit='mm', $format='A4')
{
	//Call parent constructor
	parent::__construct($orientation,$unit,$format);
	//Initialization
	$this->B=0;
	$this->I=0;
	$this->U=0;
	$this->HREF='';
}

	public function Header()
	{

		$cl = \LW\Settings::currentUser();

        $club_data = get_term_meta($_GET["tag_ID"]);
		$clc = get_term($_GET["tag_ID"],'clubs');

		if($cl["club_taxonomy"]->name){
			$title = 'Lummi Island Wild - '.$cl["club_taxonomy"]->name;
		}elseif ($club_data["name"][0]){
            $title = 'Lummi Island Wild - '.$club_data["name"][0];
        }elseif($clc->name){
			$title = 'Lummi Island Wild - '.$clc->name;
		}
		// Arial bold 15
		$this->SetFont('Arial','',12);
		// Move to the right
		$this->Cell(80);
		// Title
		$this->Cell(30,10,$title,0,0,'C');
		$this->Cell(-180,30,'Date: '.date('m-d-Y'),0,0,'C');
		// Line break
		$this->Ln(20);
	}

function WriteHTML2($html)
{
	//HTML parser
	$html=str_replace("\n",' ',$html);
	$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	foreach($a as $i=>$e)
	{
		if($i%2==0)
		{
			//Text
			if($this->HREF)
				$this->PutLink($this->HREF,$e);
			else
				$this->Write(5,$e);
		}
		else
		{
			//Tag
			if($e[0]=='/')
				$this->CloseTag(strtoupper(substr($e,1)));
			else
			{
				//Extract attributes
				$a2=explode(' ',$e);
				$tag=strtoupper(array_shift($a2));
				$attr=array();
				foreach($a2 as $v)
				{
					if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
						$attr[strtoupper($a3[1])]=$a3[2];
				}
				$this->OpenTag($tag,$attr);
			}
		}
	}
}

function OpenTag($tag, $attr)
{
	//Opening tag
	if($tag=='B' || $tag=='I' || $tag=='U')
		$this->SetStyle($tag,true);
	if($tag=='A')
		$this->HREF=$attr['HREF'];
	if($tag=='BR')
		$this->Ln(5);
	if($tag=='P')
		$this->Ln(10);
}

function CloseTag($tag)
{
	//Closing tag
	if($tag=='B' || $tag=='I' || $tag=='U')
		$this->SetStyle($tag,false);
	if($tag=='A')
		$this->HREF='';
	if($tag=='P')
		$this->Ln(10);
}

function SetStyle($tag, $enable)
{
	//Modify style and select corresponding font
	$this->$tag+=($enable ? 1 : -1);
	$style='';
	foreach(array('B','I','U') as $s)
		if($this->$s>0)
			$style.=$s;
	$this->SetFont('',$style);
}

function WriteTable($data, $w, $attr = array())
{
	$this->SetLineWidth(0);
	$this->SetFillColor(255,255,255);
	$this->SetTextColor(0);
	$this->SetFont('');

	foreach($data as $kr => $row)
	{

		if( $kr > 0 && count($attr) > 0){

			foreach ($attr as $k => $v){

				if( trim($row[$k]) ){
					$this->SetFont($v["font-family"],$v["font-weight"],$v["font-size"]);
				}else{
					$this->SetFont('Arial','','10');
				}
			}
		}

		$nb=0;
		for($i=0;$i<count($row);$i++)
			$nb=max($nb,$this->NbLines($w[$i],trim($row[$i])));
		$h=5*$nb;
		$this->CheckPageBreak($h);
		for($i=0;$i<count($row);$i++)
		{
			$x=$this->GetX();
			$y=$this->GetY();
            $this->SetDrawColor(187,187,187);
			$this->Rect($x,$y,$w[$i],$h,'FD');
			if( count($attr[$i]) > 0 ){
				$this->SetFont($attr[$i]["font-family"],$attr[$i]["font-weight"],$attr[$i]["font-size"]);
			}else{
				$this->SetFont('Arial','','10');
			}
			$this->MultiCell($w[$i],5,trim($row[$i]),0,'L');
			//Put the position to the right of the cell
			$this->SetXY($x+$w[$i],$y);
		}
		$this->Ln($h);

	}
}

function NbLines($w, $txt)
{
	//Computes the number of lines a MultiCell of width w will take
	$cw=&$this->CurrentFont['cw'];
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);
	if($nb>0 && $s[$nb-1]=="\n")
		$nb--;
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$nl=1;
	while($i<$nb)
	{
		$c=$s[$i];
		if($c=="\n")
		{
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
			continue;
		}
		if($c==' ')
			$sep=$i;
		$l+=$cw[$c];
		if($l>$wmax)
		{
			if($sep==-1)
			{
				if($i==$j)
					$i++;
			}
			else
				$i=$sep+1;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
		}
		else
			$i++;
	}
	return $nl;
}

function CheckPageBreak($h)
{
	//If the height h would cause an overflow, add a new page immediately
	if($this->GetY()+$h>$this->PageBreakTrigger)
		$this->AddPage($this->CurOrientation);
}

function ReplaceHTML($html)
{
	$html = str_replace( '<li>', "\n<br> - " , $html );
	$html = str_replace( '<LI>', "\n - " , $html );
	$html = str_replace( '</ul>', "\n\n" , $html );
	$html = str_replace( '<strong>', "<b>" , $html );
	$html = str_replace( '</strong>', "</b>" , $html );
	$html = str_replace( '&#160;', "\n" , $html );
	$html = str_replace( '&nbsp;', " " , $html );
	$html = str_replace( '&quot;', "\"" , $html ); 
	$html = str_replace( '&#39;', "'" , $html );
	$html = iconv('UTF-8', 'windows-1252', html_entity_decode($html));

	return $html;
}

function ParseTable($Table)
{
	$_var='';
	$htmlText = $Table;
	$parser = new HtmlParser ($htmlText);
	while ($parser->parse())
	{
		if(strtolower($parser->iNodeName)=='table')
		{
			if($parser->iNodeType == NODE_TYPE_ENDELEMENT)
				$_var .='/::';
			else
				$_var .='::';
		}

		if(strtolower($parser->iNodeName)=='tr')
		{
			if($parser->iNodeType == NODE_TYPE_ENDELEMENT)
				$_var .='!-:'; //opening row
			else
				$_var .=':-!'; //closing row
		}
		if(strtolower($parser->iNodeName)=='td' && $parser->iNodeType == NODE_TYPE_ENDELEMENT)
		{
			$_var .='#,#';
		}
		if ($parser->iNodeName=='Text' && isset($parser->iNodeValue))
		{
			$_var .= $parser->iNodeValue;
		}
	}
	$elems = explode(':-!',str_replace('/','',str_replace('::','',str_replace('!-:','',$_var)))); //opening row
	foreach($elems as $key=>$value)
	{
		if(trim($value)!='')
		{
			$elems2 = explode('#,#',$value);
			array_pop($elems2);
			$data[] = $elems2;
		}
	}
	return $data;
}

function WriteHTML($html,$w=null, $attr = array())
{
	$html = $this->ReplaceHTML($html);
	//Search for a table
	$start = strpos(strtolower($html),'<table');
	$end = strpos(strtolower($html),'</table');
	if($start!==false && $end!==false)
	{
		$this->WriteHTML2(substr($html,0,$start).'<BR>');

		$tableVar = substr($html,$start,$end-$start);
		$tableData = $this->ParseTable($tableVar);
		if(!$w){
			for($i=1;$i<=count($tableData[0]);$i++)
			{
			if($this->CurOrientation=='L')
				$w[] = abs(120/(count($tableData[0])-1))+25;
			else
				$w[] = abs(120/(count($tableData[0])-1))+8;
			}
		}
		
		$this->WriteTable($tableData,$w, $attr);

		$this->WriteHTML2(substr($html,$end+8,strlen($html)-1).'<BR>');
	}
	else
	{
		$this->WriteHTML2($html);
	}
}

}