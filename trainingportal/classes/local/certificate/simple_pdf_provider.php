<?php
namespace local_trainingportal\local\certificate;
defined('MOODLE_INTERNAL') || die();

class simple_pdf_provider implements provider_interface {
    public function generate(\stdClass $application, \stdClass $offering, string $certificatenumber): string {
        global $CFG;
        require_once($CFG->libdir . '/pdflib.php');
        $pdf = new \pdf();
        $pdf->setPrintHeader(false); $pdf->setPrintFooter(false); $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 22); $pdf->Cell(0, 20, 'CERTIFICATE OF PARTICIPATION', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 13); $pdf->Ln(12); $pdf->Cell(0, 10, 'This certifies that', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 18); $pdf->Cell(0, 14, fullname($application), 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 13); $pdf->Ln(8); $pdf->Cell(0, 10, 'participated in', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 16); $pdf->Cell(0, 12, $offering->title, 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 11); $pdf->Ln(12); $pdf->Cell(0, 8, 'Certificate number: ' . $certificatenumber, 0, 1, 'C');
        $pdf->Cell(0, 8, 'Issued: ' . userdate(time(), get_string('strftimedate')), 0, 1, 'C');
        return $pdf->Output('', 'S');
    }
}
