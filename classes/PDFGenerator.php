<?php
namespace PDF\Generator;
use Spipu\Html2Pdf\Html2Pdf;
class PDFGenerator
{
	public static function generatePDF($fileName, $template, $orientation, $format, $dest)
	{
		$html2pdf = new Html2Pdf($orientation, $format, 'fr');
		$html2pdf->writeHTML($template);

		if($dest === 'S')
		{
			return $html2pdf->output($fileName, $dest);
		}
		$html2pdf->output($fileName, $dest);
	}
}
