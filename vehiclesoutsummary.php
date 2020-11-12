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


    mysqli_set_charset($link, "utf8");
    $sql = "";

    if (!isset($_POST["filterby"]) or $_POST["filterby"] == "0") {
        $sql = "select ct.customertype , c.partyName , count(kg.id) 'TotalVehiclesOut' , sum(ckl.WeightInTons - kg.WeightInTons) 'totaltons'
	, sum(ckl.TotalPrice) 'totalprice'	from customertypes ct join companies c on ct.Id = c.customertypeid
	join kanta_general kg on kg.CompanyId = c.id join company_kanta_log ckl on kg.id = ckl.Kanta_General_Id
where kg.EntryDateTime >= ? and kg.entrydatetime < date_add(?,interval 1 day)
group by ct.customertype , c.partyname order by ct.customertype;";
    } else {
        $sql = "select ct.customertype , c.partyName , count(kg.id) 'TotalVehiclesOut' , sum(ckl.WeightInTons - kg.WeightInTons) 'totaltons'
        , sum(ckl.TotalPrice) 'totalprice'	from customertypes ct join companies c on ct.Id = c.customertypeid
        join kanta_general kg on kg.CompanyId = c.id join company_kanta_log ckl on kg.id = ckl.Kanta_General_Id
    where kg.EntryDateTime >= ? and kg.entrydatetime < date_add(?,interval 1 day) and ct.id = ?
    group by ct.customertype , c.partyname order by ct.customertype;";
    }

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        if (!isset($_POST["filterby"]) or $_POST["filterby"] == "0") {
            mysqli_stmt_bind_param($stmt, "ss", $FromDate, $ToDate);
        } else {
            mysqli_stmt_bind_param($stmt, "sss", $FromDate, $ToDate, $CustomerTypeId);
        }

        if (!isset($_POST["kt_datepicker_1"])) {
            $FromDate = Date('Y-m-d');
            $ToDate = Date('Y-m-d');
        } else {
            $FromDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_1"]));
            $ToDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_2"]));
        }

        if (!isset($_POST["filterby"])) {
            $CustomerTypeId = 0;
        } else {
            $CustomerTypeId = $_POST["filterby"];
        }

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt,  $CustomerType, $PartyName, $TotalVehiclesOut, $TotalTons, $TotalAmount);

            if (mysqli_stmt_num_rows($stmt) > 0) {



                while (mysqli_stmt_fetch($stmt)) : ?>


<tr>
    <td class="noori-normal-12"> <?php echo $PartyName; ?> </td>
    <td> <?php echo number_format($TotalVehiclesOut) ?> </td>
    <td> <?php echo number_format($TotalTons) ?> </td>
    <td> <?php echo number_format($TotalAmount) ?> </td>
</tr>

<?php $GLOBALS['_TotalValue'] += $TotalAmount;
                    $GLOBALS['_TotalTons'] += $TotalTons;
                    $GLOBALS['_TotalVehicles'] += $TotalVehiclesOut;
                endwhile;
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

                                                <div class="card-body">
                                                    <form method="post" action="vehiclesoutsummary.php">
                                                        <div class="form-group row">
                                                            <div class="col-lg-3 mt-4">
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
                                                            <div class="col-lg-3 mt-4">
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
                                                            <div class="col-lg-3 mt-4">
                                                                <label> Filter By </label>
                                                                <select class="form-control" id="filterby"
                                                                    name="filterby">
                                                                    <option value="0"
                                                                        <?php echo (isset($_POST["filterby"]) and $_POST["filterby"] == "0") ? 'selected = "selected"' : ''; ?>>
                                                                        دونوں</option>
                                                                    <option value="1"
                                                                        <?php echo (isset($_POST["filterby"]) and $_POST["filterby"] == "1") ? 'selected = "selected"' : ''; ?>>

                                                                        کمپنی</option>
                                                                    <option value="2"
                                                                        <?php echo (isset($_POST["filterby"]) and $_POST["filterby"] == "2") ? 'selected = "selected"' : ''; ?>>
                                                                        کسٹمر</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-lg-3 mt-12">


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

                                                                <th>نام</th>
                                                                <th>ٹوٹل گاڑیاں</th>
                                                                <th>ٹوٹل ٹن</th>
                                                                <th>ٹوٹل رقم</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php


                                                            DisplayAttendences();



                                                            ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td></td>
                                                                <td><?php echo number_format($GLOBALS['_TotalVehicles']); ?>
                                                                </td>
                                                                <td><?php echo number_format($GLOBALS['_TotalTons']); ?>
                                                                </td>
                                                                <td><?php echo number_format($GLOBALS['_TotalValue']); ?>
                                                                </td>
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