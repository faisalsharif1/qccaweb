<?php


// Initialize the session
session_start();

$_TotalDebit = 0;
$_TotalCredit = 0;

if (!isset($_SESSION["loggedin"]) && !$_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}



function DisplayCompaniesInDashBoard()
{
    $InvoiceInProcessing = "";
    $LastInvoice = "";


    require "config.php";

    mysqli_set_charset($link, "utf8");

    // Prepare a select statement
    $sql = "SELECT DISTINCT t.invoiceNumber , t.SerialNo , DATE_FORMAT(t.TransactionDate , '%d-%m-%Y') AS
    TransactionDate
     FROM accounts a JOIN transactions t ON a.AccountId = t.AccountId left JOIN kanta_general kg on t.invoicenumber = kg.invoicenumber
WHERE  cast(t.TransactionDate as date) >= ? AND cast(t.TransactionDate as date) < date_add(?,INTERVAL 1 DAY) and t.IsApproved = 1 and kg.id is null ORDER BY InvoiceNumber";

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

            mysqli_stmt_bind_result($stmt,  $InvoiceNumber, $SerialNo, $TransactionDate);

            if (mysqli_stmt_num_rows($stmt) > 0) {



                while (mysqli_stmt_fetch($stmt)) : ?>

<div class="col-xl-12">
    <!--begin::List Widget 11-->
    <div class="card card-custom card-stretch gutter-b">
        <!--begin::Header-->

        <div class="card-header border-0 bg-light-primary">
            <h3 class="card-title font-weight-bolder text-dark"> Serial No
                <?php echo $SerialNo ?> </h3>


            <div class="card-toolbar">
                <div class="dropdown dropdown-inline" data-toggle="tooltip" title="" data-placement="left"
                    data-original-title="Quick actions">
                    <a href="#" class="btn btn-clean btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="ki ki-bold-more-ver"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
                        <!--begin::Navigation-->
                        <ul class="navi navi-hover py-5">



                        </ul>
                        <!--end::Navigation-->
                    </div>
                </div>
            </div>
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body pt-0 bg-gradient-light">
            <!--begin::Item-->
            <span class="label mb-4 label-lg label-light-primary label-inline font-weight-bold mt-5">
                <?php echo $TransactionDate; ?>
            </span>

            <?php DisplayInvoiceDetails($link, $InvoiceNumber); ?>

            <!--end::Item-->
        </div>
        <!--end::Body-->
    </div>
    <!--end::List Widget 11-->
</div>



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

function DisplayInvoiceDetails($link, $fnInvNumber)
{
    $InvoiceInProcessing = "";
    $LastInvoice = "";


    // Prepare a select statement
    $sql = "SELECT a.AccountTitle , t.Particulars , t.Amount , m.minedescription , 
            CASE WHEN t.incexptag = 0 then '' when t.incexptag = 1 then 'income' else 'expense' end 'entrytype'
         FROM accounts a JOIN transactions t ON a.AccountId =
    t.AccountId left join mine m on m.id = t.mineid
    WHERE t.InvoiceNumber=" . $fnInvNumber;

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        // mysqli_stmt_bind_param($stmt, "d", $param_invoicenumber);

        // Set parameters
        $param_invoicenumber = $fnInvNumber;

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            $bindresult = mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt, $AccountTitle, $Particulars, $Amount, $MineDescription, $EntryType);



            if (mysqli_stmt_num_rows($stmt) > 0) {

                while (mysqli_stmt_fetch($stmt)) : ?>

                    <?php $GLOBALS["_TotalDebit"] += $Amount > 0 ? $Amount : 0; $GLOBALS["_TotalCredit"]+=$Amount < 0 ? $Amount : 0; ?>

<div class="d-flex align-items-center mb-9 bg-light-warning rounded p-5">
    <!--begin::Icon-->
    <span class="svg-icon svg-icon-warning mr-5">
        <span class="svg-icon svg-icon-lg">
            <!--begin::Svg Icon | path:assets/media/svg/icons/Home/Library.svg-->
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect x="0" y="0" width="24" height="24"></rect>
                    <path
                        d="M5,3 L6,3 C6.55228475,3 7,3.44771525 7,4 L7,20 C7,20.5522847 6.55228475,21 6,21 L5,21 C4.44771525,21 4,20.5522847 4,20 L4,4 C4,3.44771525 4.44771525,3 5,3 Z M10,3 L11,3 C11.5522847,3 12,3.44771525 12,4 L12,20 C12,20.5522847 11.5522847,21 11,21 L10,21 C9.44771525,21 9,20.5522847 9,20 L9,4 C9,3.44771525 9.44771525,3 10,3 Z"
                        fill="#000000"></path>
                    <rect fill="#000000" opacity="0.3"
                        transform="translate(17.825568, 11.945519) rotate(-19.000000) translate(-17.825568, -11.945519)"
                        x="16.3255682" y="2.94551858" width="3" height="18" rx="1"></rect>
                </g>
            </svg>
            <!--end::Svg Icon-->
        </span>
    </span>
    <!--end::Icon-->
    <!--begin::Title-->
    <div class="d-flex flex-column flex-grow-1 mr-2">

        <a href="#" class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">
            <?php echo $AccountTitle; ?></a>
        <span class=" font-weight-bold menu-title"><?php echo $Particulars; ?></span>
    </div>

    <?php if ($Amount > 0 && strlen($MineDescription) > 0) { ?>
    <div class="d-flex flex-column flex-grow-1 mr-2">

        <a href="#" class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">
            <?php echo "Mine Selected"; ?></a>
        <span class=" font-weight-bold menu-title"><?php echo $MineDescription; ?></span>
    </div>
    <?php } ?>

    <?php if ($Amount > 0 && strlen($EntryType) > 0) { ?>
    <div class="d-flex flex-column flex-grow-1 mr-2">

        <a href="#" class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">
            <?php echo "Entry Type"; ?></a>
        <span class=" font-weight-bold menu-title"><?php echo $EntryType; ?></span>
    </div>
    <?php } ?>

    <!--end::Title-->
    <!--begin::Lable-->
    <span class="font-weight-bolder text-warning py-1 font-size-lg"> <?php echo number_format($Amount); ?></a> </span>
</div>



<?php endwhile;
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    } else {
        echo mysqli_error($link);;
    }

    // Close connectio
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

                                            <form method="post" action="approvedentries.php">
                                                <div class="form-group row">
                                                    <div class="col-lg-4 mt-4">
                                                        <label> From Date :</label>
                                                        <input type="text" class="form-control" id="kt_datepicker_1"
                                                            name="kt_datepicker_1" readonly="readonly"
                                                            placeholder="Select date"
                                                            value="<?php if (!isset($_POST["kt_datepicker_1"])) {
                                                                                                                                                                                                        echo date('m/1/Y');
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        echo Date('m/d/Y', strtotime($_POST["kt_datepicker_1"]));
                                                                                                                                                                                                    } ?>">
                                                    </div>
                                                    <div class="col-lg-4 mt-4">
                                                        <label>To Date:</label>
                                                        <input type="text" class="form-control" id="kt_datepicker_2" ,
                                                            name="kt_datepicker_2" readonly="readonly"
                                                            placeholder="Select date"
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
                                    <!--Begin::Row-->
                                    <div class="row">
                                        <?php DisplayCompaniesInDashBoard(); ?>
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
    <!--begin::Global Theme Bundle(used by all pages)-->
    <?php include_once("partials/_footer-javascript.php"); ?>

    <script src="assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js"></script>
    <script type="text/javascript">

    </script>
</body>
<!--end::Body-->

</html>