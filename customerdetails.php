<?php


// Initialize the session
session_start();

$_TotalTons = 0;
$_TotalValue = 0;
$_TotalVehicles = 0;

if (!isset($_SESSION["loggedin"]) && !$_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}



function DisplayCompanyDetails()
{
    $FromDate = Date('Y-m-d');
    $ToDate = Date('Y-m-d');
    $CompanyId = 0;

    if (isset($_POST["kt_datepicker_1"])) {
        $FromDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_1"]));
    } else {
        if (isset($_GET["Date"])){
            $FromDate = Date('Y-m-d',strtotime($_GET["Date"]));
        }
        else {
            $FromDate = Date('Y-m-d');
        }
    }

    if (isset($_POST["kt_datepicker_2"])) {
        $ToDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_2"]));
    } else {
        if (isset($_GET["Date"])){
            $ToDate = Date('Y-m-d',strtotime($_GET["Date"]));
        }
        else {
            $ToDate = Date('Y-m-d');
        }
    }

    if (isset($_POST["CompanyId"])) {
        $CompanyId = $_POST["CompanyId"];
    } else {
        if (isset($_GET["CompanyId"])) {
            $CompanyId = $_GET["CompanyId"];
        } else {
            $CompanyId = 0;
        }
    }

    require "config.php";
    // Prepare a select statement
    $sql = "select  DATE_FORMAT(kg.EntryDateTime,'%d-%m-%Y') 'EnryDateTime' , ckl.SerialId , kg.VehicleNumber ,  ckl.WeightInTons - kg.WeightInTons 'TotalWeight' , ckl.TotalPrice
	from companies c JOIN kanta_general kg ON c.id = kg.companyId
		JOIN company_kanta_log ckl ON kg.id = ckl.Kanta_General_id		
            WHERE kg.EntryDateTime >= ? and kg.entrydatetime < date_add(?,INTERVAL 1 DAY) AND kg.companyId = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "sss", $FromDate, $ToDate, $CompanyId);

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt,  $EntryDateTime, $SerialId, $VehicleNumber, $TotalTons, $TotalAmount);

            if (mysqli_stmt_num_rows($stmt) > 0) {

                while (mysqli_stmt_fetch($stmt)) : ?>


<tr>
    <td> <?php echo $EntryDateTime ?> </td>
    <td> <?php echo $SerialId ?> </td>
    <td> <?php echo $VehicleNumber ?> </td>
    <td> <?php echo $TotalTons ?> </td>
    <td> <?php echo Number_Format($TotalAmount) ?> </td>
</tr>

<?php $GLOBALS['_TotalValue'] += $TotalAmount; $GLOBALS["_TotalTons"] += $TotalTons; $GLOBALS["_TotalVehicles"] += 1; endwhile;
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
}

?>


<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <base href="">
    <meta charset="utf-8" />
    <title> Entries </title>
    <meta name="description" content="Description here" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <?php include_once("partials/_header-stylesheets.php"); ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">

</head>

<!--end::Head-->

<!--begin::Body-->

<body id="kt_body"
    class="page-loading-enabled page-loading header-fixed header-mobile-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">

    <!--begin::Page Loader-->
    <?php include("partials/_page-loader.php"); ?>

    <!--begin::Mobile Header-->
    <?php include("partials/_header-mobile.php"); ?>
    <div class="d-flex flex-column flex-root">
        <!--begin::Page-->
        <div class="d-flex flex-row flex-column-fluid page">

            <!--begin::sidebar menu-->
            <?php include("partials/_aside.php"); ?>
            <!--begin::Wrapper-->
            <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">

                <!--begin::Header File-->
                <?php include("partials/_header.php"); ?>
                <!--begin::Content-->
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <!--begin::Content File-->
                    <!--begin::Entry-->
                    <div class="d-flex flex-column-fluid">
                        <!--begin::Container-->
                        <div class="container-fluid">
                            <!--begin::Row-->

                            <div class="d-flex flex-column-fluid">
                                <!--begin::Container-->
                                <div class="container">
                                    <div class="row mb-4">
                                        <div class="col-lg-12">

                                            <div class="card card-primary">
                                                <div class="card-header">
                                                    <div class="card-title">
                                                        <h3 class="card-label dashboard-button-title"> کسٹمرز کے گاڑیوں
                                                            کی ترسیل کا دورانیہ
                                                        </h3>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <form method="post" action="customerdetails.php">
                                                        <div class="form-group row">
                                                            <div class="col-lg-4 mt-4">
                                                                <label> From Date :</label>
                                                                <input type="text" class="form-control"
                                                                    id="kt_datepicker_1" name="kt_datepicker_1"
                                                                    readonly="readonly" placeholder="Select date"
                                                                    value="<?php if (!isset($_POST["kt_datepicker_1"])) {
                                                                                                                                                                                                                echo date('m/d/Y');
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                echo Date('m/d/Y', strtotime($_POST["kt_datepicker_1"]));
                                                                                                                                                                                                            } ?>">
                                                            </div>
                                                            <div class="col-lg-4 mt-4">
                                                                <label>To Date:</label>
                                                                <input type="text" class="form-control"
                                                                    id="kt_datepicker_2" , name="kt_datepicker_2"
                                                                    readonly="readonly" placeholder="Select date"
                                                                    value="<?php if (!isset($_POST["kt_datepicker_2"])) {
                                                                                                                                                                                                                echo date('m/d/Y');
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                echo Date('m/d/Y', strtotime($_POST["kt_datepicker_2"]));
                                                                                                                                                                                                            } ?>">

                                                            </div>

                                                            <div class="col-lg-4 mt-12">
                                                                <input type="hidden" name="CompanyId" id="CompanyId"
                                                                    value="<?php echo isset($_GET["CompanyId"]) ? $_GET["CompanyId"] : $_POST["CompanyId"] ?>">

                                                                <button type="submit"
                                                                    class="btn btn-success mr-2">Submit</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>



                                        </div>

                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-lg-12">
                                            <div class="card card-primary">
                                                <div class="card-body">

                                                    <table id="tbl" class="table">
                                                        <thead>
                                                            <tr>
                                                                <th class="noori-normal"> تاریخ</th>
                                                                <th class="noori-normal"> سیریل</th>
                                                                <th class="noori-normal"> گاڑی کا نمبر</th>
                                                                <th class="noori-normal"> وزن </th>
                                                                <th class="noori-normal"> قیمت </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php


                                                            DisplayCompanyDetails();



                                                            ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
<td></td>
<td>ٹوٹل</td>
<td><?php echo number_format($GLOBALS['_TotalVehicles']); ?></td>
<td><?php echo number_format($GLOBALS['_TotalTons']); ?></td>
<td><?php echo number_format($GLOBALS['_TotalValue']); ?></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!--end::Content-->

                <!--begin::Footer-->
                <?php include("partials/_footer.php"); ?>
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::Main-->

    <!--begin::Scroll to top-->
    <?php include("partials/_extras/scrolltop.php"); ?>
    <?php include_once("partials/_footer-javascript.php"); ?>
    <script src="assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js">
    </script>

    <script type="text/javascript">
    $(document).ready(function() {


    });
    </script>
</body>
<!--end::Body-->

</html>