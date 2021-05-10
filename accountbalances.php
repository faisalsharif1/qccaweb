<?php


// Initialize the session
session_start();

$_TotalValue = 0;

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



function DisplayLedger()
{

    require "config.php";

    mysqli_set_charset($link, "utf8");
    $sql = "";


    $sql = "select row_number() over (order by a.accounttitle) as abc , a.accounttitle , sum(tr.amount) 'balance',a.accountid
    from heads h join categories c on h.id = c.headid join accounts a on c.categoryid = a.categoryid 
    join transactions tr on a.accountid = tr.accountid where cast(transactiondate as date) >= ? and cast(transactiondate as date) < date_add(?,interval 1 day)
    and h.id = case when ? = 0 then h.id else ? end and c.categoryid = case when ? = 0 then c.categoryid else ? end
    group by a.accounttitle,a.accountid";


    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ssssss", $FromDate, $ToDate , $AccountId , $AccountId , $CategoryId , $CategoryId);

        if (!isset($_POST["kt_datepicker_1"])) {
            $FromDate = Date('Y-m-d');
            $ToDate = Date('Y-m-d');
        } else {
            $FromDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_1"]));
            $ToDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_2"]));
        }

        if (!isset($_POST["filterby"])) {
            $AccountId = 0;
        } else {
            $AccountId = $_POST["filterby"];
        }

        if (!isset($_POST["filtercategory"])) {
            $CategoryId = 0;
        } else {
            $CategoryId = $_POST["filtercategory"];
        }

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result( $stmt ,$abc, $accounttitle, $balance , $accountid);

            if (mysqli_stmt_num_rows($stmt) > 0) {


                while (mysqli_stmt_fetch($stmt)) : ?>


<tr>
    <td> <?php echo $abc; ?> </td>
    <td> <a href="partyledger.php?fromdate=<?php echo htmlentities(strtotime($_POST["kt_datepicker_1"])); ?>&todate=<?php echo htmlentities(strtotime($_POST["kt_datepicker_2"])); ?>&accountid=<?php echo $accountid;?>"><?php echo $accounttitle ?> </a></td>
    <td> <?php echo number_format($balance) ?> </td>
</tr>

<?php
$GLOBALS['_TotalValue'] += $balance;
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

function DisplayHeads()
{

    require "config.php";

    mysqli_set_charset($link, "utf8");
    $sql = "";

    $sql = "SELECT id , headname FROM heads";

    if ($stmt = mysqli_prepare($link, $sql)) {

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt,  $AccountId, $AccountTitle);

            if (mysqli_stmt_num_rows($stmt) > 0) {

                while (mysqli_stmt_fetch($stmt)) {
                    if (isset($_POST['filterby']) && $_POST['filterby'] == $AccountId) {
                        echo "<option value= " . $AccountId . " selected>" . $AccountTitle . "</option>";
                    } else {
                        echo "<option value= " . $AccountId . ">" . $AccountTitle . "</option>";
                    }
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
}

function DisplayControlAccounts()
{

    require "config.php";

    mysqli_set_charset($link, "utf8");
    $sql = "";


    $sql = "SELECT categoryid , categoryname FROM categories";

    if ($stmt = mysqli_prepare($link, $sql)) {

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt,  $AccountId, $AccountTitle);

            if (mysqli_stmt_num_rows($stmt) > 0) {

                while (mysqli_stmt_fetch($stmt)) {
                    if (isset($_POST['filtercategory']) && $_POST['filtercategory'] == $AccountId) {
                        echo "<option value= " . $AccountId . " selected>" . $AccountTitle . "</option>";
                    } else {
                        echo "<option value= " . $AccountId . ">" . $AccountTitle . "</option>";
                    }
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

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <?php include_once("partials/_header-stylesheets.php"); ?>

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
                                                    <form method="post" action="accountbalances.php">
                                                        <div class="form-group row">

                                                            <div class="col-lg-3 mt-4">
                                                                <label> From Date :</label>
                                                                <input type="text" class="form-control"
                                                                    id="kt_datepicker_1" name="kt_datepicker_1"
                                                                    readonly="readonly" placeholder="Select date"
                                                                    value="<?php if (!isset($_POST["kt_datepicker_1"])) {
                                                                                                                                                                                                                echo date('m/d/2000');
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
                                                            <div class="col-lg-5 mt-4">
                                                                <label> Filter By </label>
                                                                <select class="form-control select2" id="filterby"
                                                                    name="filterby">
                                                                  <option value="0" selected>Show All</option>                                                         
                                                                    <?php DisplayHeads() ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-lg-5 mt-4">
                                                                <label> Filter By </label>
                                                                <select class="form-control select2" id="filtercategory"
                                                                    name="filtercategory">
                                                                    <option value="0" selected>Show All</option> 
                                                                    <?php DisplayControlAccounts() ?>
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
                                                    <div class="table-responsive">
                                                        <table id="tbl" class="table table-striped">
                                                            <thead>
                                                                <tr>                        
                                                                    <th style="width:80px;"> سیریل </th>
                                                                    <th style="width:70%" > کھاتہ </th>
                                                                    <th style="width:141;"> بیلنس </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php


                                                                DisplayLedger();



                                                                ?>
                                                            </tbody>
                                                            <tfoot>
                                                                <td></td>
                                                                <td>ٹوٹل</td>
                                                                <td><?php echo number_format($GLOBALS['_TotalValue']); ?></td>                                                                                                                                                                                                            
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js">
    </script>

    <script type="text/javascript">
    $(document).ready(function() {


        $('#filterby').select2({
            width: "100%"
        });

        $('#filtercategory').select2({
            width: "100%"
        });

        $('#tbl').DataTable({

            paging: false,
            targets: 'no-sort',
            bSort: false,
            order: []
        });
    });
    </script>
</body>
<!--end::Body-->

</html>