<?php


// Initialize the session
session_start();

$_TotalExpenses = 0;
$_TotalIncomeCompanies = 0;
$_TotalIncomeOthers = 0;

if (!isset($_SESSION["loggedin"]) && !$_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}



$querytext = "";

function DisplayIncome()
{
    require "config.php";


    mysqli_set_charset($link, "utf8");
    $sql = "";

        $sql = "select i.itemname , u.unitname , sum(s.quantity) 'totalreceived' , sum(s.consumed) 'totalassigned' , sum(s.quantity-s.consumed) 'totalavailable',
		sum((s.quantity - s.consumed) * s.unitprice) 'totalvalue' 
		from items i join stocks s on i.id = s.itemid join units u on u.id = s.unitid
		where i.id = case when ? = 0 then i.id else ? end
	group by i.itemname , u.unitname";
    

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $ItemId,$ItemId);

        if (isset($_POST["ItemId"]))
        {
            $ItemId = $_POST["ItemId"];
        }
        else
        {
            $ItemId = 0;
        }

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt,  $ItemName , $UnitName , $TotalReceived , $TotalAssigned , $TotalAvailable , $TotalValue);

            if (mysqli_stmt_num_rows($stmt) > 0) {



                while (mysqli_stmt_fetch($stmt)) : ?>


<tr>
    <td> <?php echo $ItemName; ?> </td>
    <td> <?php echo $UnitName; ?> </td>
    <td> <?php echo number_format($TotalReceived,2) ?> </td>
    <td> <?php echo number_format($TotalAssigned,2) ?> </td>
    <td> <?php echo number_format($TotalAvailable,2) ?> </td>
    <td> <?php echo number_format($TotalValue,2) ?> </td>
</tr>

<?php 
                    $GLOBALS['_TotalIncomeCompanies'] += $TotalIncomeAmount;
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

function DisplayItems()
{

    require "config.php";

    mysqli_set_charset($link, "utf8");
    $sql = "";

    $sql = "select Id, ItemName from items";

    if ($stmt = mysqli_prepare($link, $sql)) {

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt,  $Id, $ItemName);

            if (mysqli_stmt_num_rows($stmt) > 0) {

                while (mysqli_stmt_fetch($stmt)) {
                    if (isset($_POST['ItemId']) && $_POST['ItemId'] == $Id) {
                        echo "<option value= " . $Id . " selected>" . $ItemName . "</option>";
                    } else {
                        echo "<option value= " . $Id . ">" . $ItemName . "</option>";
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
    <title> آمدن اور اخراجات  </title>
    <meta name="description" content="Description here" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
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
                                                    <form method="post" action="stockreport.php">
                                                        <div class="form-group row">
                                                           
                                                        <div class="col-lg-5 mt-4">
                                                                <label> سٹاک رپورٹ </label>
                                                                <select class="form-control select2" id="ItemId"
                                                                    name="ItemId">
                                                                  <option value="0" selected>Show All</option>                                                         
                                                                    <?php DisplayItems() ?>
                                                                </select>
                                                            </div>
                                                           
                                                                                                                    
                                                            <div class="col-lg-3 mt-12">
                                                                <button type="submit"
                                                                    class="btn btn-success mr-2"> رپورٹ </button>
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
                                            <div class="card-header">
                                            <h1> اخراجات  </h1>
                                            </div>
                                                <div class="card-body">

                                                    <table id="tbl" class="table">
                                                        <thead>
                                                            <tr>

                                                                <th> آئٹم </th>
                                                                <th> مقدار کی قسم </th>
                                                                <th>  کل آمد</th>
                                                                <th>  کل استعمال</th>
                                                                <th> موجودہ تعداد </th>
                                                                <th> موجودہ حیثیت</th>

                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                                DisplayIncome();
                                                            ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td> ٹوٹل اخراجات</td>
                                                                <td><?php echo number_format($GLOBALS['_TotalExpenses']); ?> </td> 
                                                                <input type="hidden" id="expense" value="<?php echo number_format($GLOBALS['_TotalExpenses']); ?>">                                        
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js">
    </script>

    <script type="text/javascript">
    $(document).ready(function() {

       

        $('#ItemId').select2({
            width: "100%"
        });

       
        $('#tbl').DataTable({
            paging: false,
            "order": [
                [2, "desc"]
            ]
        });

        $('#tbl2').DataTable();
    });
    </script>
</body>
<!--end::Body-->

</html>