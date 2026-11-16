<?php
require('./fpdf17/fpdf.php');
session_start();
class Products {
    public function all() {
        try {
            $data = $_POST;
			
			$date = $_SESSION['date'];
			$date_out = $_SESSION['date_out'];
					
			$db = new PDO('mysql:host=localhost;dbname=db_ords;charset=UTF-8', 'root', 'sys!w@nDb123');
            
			$query = $db->prepare("SELECT * FROM tbl_products WHERE product like '%$product%' 
			and date BETWEEN '$date' AND '$date_out' ORDER BY date");
			            
			$query->execute();
			$products = $query->fetchAll(PDO::FETCH_ASSOC);
			
		} catch (PDOException $e) {
              $result = false;
        }
        $query = null;
        $db = null;
        return $products;        
    }
}

class ProductsPDF extends FPDF {
    
	function Header()
{
    $this->Cell(-.5);
    //Title
		$date = $_SESSION['date'];
		$date_out = $_SESSION['date_out'];
		
		$this->SetFont('','','6');
		$this->Cell(0,10,'OTKS ONLINE REPORT SYSTEM',0,0,'L');
		$this->Ln(3);
		$this->SetFont('','B','10');
		$this->Cell(0,10,'Inconsistency Logs Report',0,0,'L');
		$this->SetFont('Arial','','6');
		$this->Cell(0,10,$date." TO ".$date_out,0,0,'R');
		$this->SetFont('','B','8');
		$this->Ln(1);
	}
	
	function Footer()
	{	
		
		$this->SetY(-40);
		//Arial italic 7
		$this->SetFont('','','7');
		$this->Cell(0,10,"Prepared By:	__________________________________",0,0,'L');
		$this->Cell(0,10,"Checked By:	_________________________________________",0,0,'R');
		$this->Ln(3);
		$this->Cell(0,10,"MIS PERSONNEL																																																																																																																																																			MIS PERSONNEL",0,0,'C');
		$this->SetFont('Times','','6');
		$this->Ln(5);
		$this->Cell(0,10,"NOTICE TO RECIPIENTS:",0,0,'L');
		$this->Ln(5);
		$this->Cell(0,10,"1) PLEASE SUBMIT TO CONCERNED EMPLOYEE FOR CORRECTION. THE EMPLOYEE SHOULD WRITE ON THE MISSING TIME LOG(TIME-IN OR TIME-OUT)",0,0,'L');
		$this->Ln(3);
		$this->Cell(0,10,"2) THE IMMEDIATE SUPERIOR SHOULD SIGN AND PRINT HIS/HER NAME ON THE APPROVAL COLUMN TO VALIDATE THE CORRECTION. MIS WILL NOT CONSIDER UNAPPROVED ITEMS",0,0,'L');
		$this->Ln(3);
		$this->Cell(0,10,"3) THIS REPORT SHOULD BE SENT BACK TO MIS IMMIDIATELY UPON COMPLETION BY THE CONCERNED DEPARTMENT",0,0,'L');
		$this->SetFont('','','6');
		$this->SetFont('Arial','I',8);
		$this->Ln(3);
		//Page number
		$this->Cell(0,10,'Page '.$this->PageNo().'',0,0,'R');
	}
	
	// Create basic table
    public function CreateTable($header, $data)
    {	
		$this->SetFont('','B','8');
		$this->Cell(0,10,'________________________________________________________________________________________________________________________',0,0,'L');
		$this->Ln(7);
        		$this->SetFont('','','6');
		// Header
        $this->SetFillColor(255);
        $this->SetTextColor(0);
        $this->SetFont('','B');
        foreach ($header as $col) {
            //Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, boolean fill [, mixed link]]]]]]])
            $this->Cell($col[1], 7, $col[0], 0, 0, 'P', true);
			//$this->MultiCell( $col[1], 7, $col[0], 1);
        }
		$this->Ln(1);
		$this->Cell(0,10,"APPROVAL",0,0,"R");
        $this->Ln(1);
        // Data
        $this->SetFillColor(255);
        $this->SetTextColor(0);
        $this->SetFont('','B','8');
		$this->Cell(0,10,'________________________________________________________________________________________________________________________',0,0,'L');
		$this->Ln();
		foreach ($data as $row)
			{
			$this->SetFont('','','6');
			$i = 0;
            foreach ($row as $field) {

				$this->Cell($header[$i][1], 6, $field, 0, 0, 'L', true);
                
				$i++;
            }
            $this->Ln();
        }

		}
		public function ICRS(){
			$this->Ln(3);
			$this->SetFont('','B','8');
			$this->Cell(0,10,"RECEIVING SHEET",0,0,'L');
		}	
		
}

	
$departments = array(
		array('CN - PPC', 30), 
		array('CN - PRODUCTION', 30), 
		array('CN - LQC', 30)
	);
	
$lines = explode("\n","");
// Column headings
$header = array(
			 array('DEPARTMENT', 30), 
             array('SECTION', 15), 
             array('DATE', 15), 
             array('EMPNO', 15),
             array('EMPLOYEE NAME', 35),
			 array('POSITION', 25),
             array('TIME-IN', 15),
			 array('TIME-OUT', 15),
			 array("IMMIDIATE SUPERIOR'S", 40),
		);
// Get data
$products = new Products();
$data = $products->all();

$pdf = new ProductsPDF('P');
$pdf->SetFont('Arial', '', 5);
$pdf->AddPage();
$pdf->ICRS();
$pdf->AddPage();
$pdf->CreateTable($header,$data);
$pdf->Output();
session_destroy();
?>