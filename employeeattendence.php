<?php


// Initialize the session
session_start();



if (!isset($_SESSION["loggedin"]) && !$_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

$FromDate;
$ToDate;

if (isset($_POST["kt_datepicker_1"])) {
    $FromDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_1"]));
}

if (isset($_POST["kt_datepicker_2"])) {
    $ToDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_2"]));
}

$querytext = "";

function DisplayAttendences()
{
    require "config.php";
    
   mysqli_set_charset($link,"utf8");  
    
    // Prepare a select statement
    $sql = "SeLeCT et.employeetypedescription 'Type' , 
    e.employeeName ,  DATe_FORMAT(ea.entryDatetime,'%d-%m-%Y') 'entryDate' , DATe_FORMAT(ea.CheckInDatetime,'%r') 'CheckIn' , 
        case when ea.CheckOutDatetime = '1900-01-01 00:00:00.000000' then null else DATe_FORMAT(ea.checkoutdatetime,'%r') end 'CheckOut' FROM employees e LeFT JOIN employeeattendences ea ON e.id = ea.employeeId
        AND (entryDatetime >= ? AND entrydatetime <  DATE_ADD(?, INTERVAL 1 DAY))
    join employeetypes et ON et.Id = e.employeetype";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ss", $FromDate, $ToDate);

        if (!isset($_POST["kt_datepicker_1"])) {
            $FromDate = Date('Y-m-d');
            $ToDate = Date('Y-m-d');
        } else {
            $FromDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_1"]));
            $ToDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_2"]));
        }
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt,  $EmployeeName, $EmployeeType, $TransactionDate, $InTime, $OutTime);

            if (mysqli_stmt_num_rows($stmt) > 0) {



                while (mysqli_stmt_fetch($stmt)) : ?>


<tr>
    <td class="noori-normal-12"> <?php echo $EmployeeType ?> </td>
    <td> <?php echo $TransactionDate ?> </td>
    <td> <?php echo $InTime ?> </td>
    <td> <?php echo $OutTime ?> </td>
</tr>

<?php endwhile;
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
                                                        <h3 class="card-label dashboard-button-title"> حاضری رپورٹ کا دورانیہ</h3>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <form method="post" action="employeeattendence.php">
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
                                                                <th> نام عہدہ</th>
                                                                <th>تاریخ</th>
                                                                <th>آنے کا ٹائم</th>
                                                                <th>جانے کا ٹائم</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php


                                                            DisplayAttendences();



                                                            ?>
                                                        </tbody>
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

        $('#tbl').DataTable({
            paging: false,
            "order": [
                [2, "desc"]
            ]
        });
    });
    </script>
</body>
<!--end::Body-->

</html>