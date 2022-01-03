<?php
namespace App\Http\Controllers\LeadController;

use Illuminate\Http\Request;
use App\Lead;

class LeadController extends Controllers
{
    public function index() {
        return view('import');
    }


    // ------------- [ Import Leads ] ----------------
    public function importLeads(Request $request) {
        $data           =       array();

        $lead_id = "";

        $NPI = "";
        $Provider_Organization_Name_Legal_Business_Name = "";

        //  file validation
        $request->validate([
            "csv_file" => "required",
        ]);

        $file = $request->file("csv_file");
        $csvData = file_get_contents($file);

        $rows = array_map("str_getcsv", explode("\n", $csvData));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            if (isset($row[0])) {
                if ($row[0] != "") {
                    $row = array_combine($header, $row);
                    $NPI = $row["NPI"];
                    $NPI_array = explode(" ", $NPI);
                    $NPI = $NPI_array[0];

                    if (isset($NPI_array[1])) {
                        $Provider_Organization_Name_Legal_Business_Name = $NPI_array[1];
                    }

                    // master lead data
                    $leadData = array(
                        "NPI" => $NPI,
                        "Provider Organization Name (Legal Business Name)" => $Provider_Organization_Name_Legal_Business_Name,
                        "Provider Last Name (Legal Name)" => $row["Provider Last Name (Legal Name)"],
                        "Provider First Name" => $row["Provider First Name"],
                        "Provider Middle Name" => $row["Provider Middle Name"],
                        "Provider Other Organization Name" => $row["Provider Other Organization Name"],
                    );

                }
            }
        }

        return back()->with($data["status"], $data["message"]);
    }
}