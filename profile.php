<?php
include 'header.php';
include 'footer.php';
$pro = $_SESSION['progress'];
?>


 <div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                           <h4>Profile</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                           <ol class="breadcrumb">
                               <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                               <li class="breadcrumb-item active" aria-current="page">Profile</li>
                           </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
                    <div class="pd-20 card-box height-100-p">
                         <div class="profile-photo">

                             <img src="vendors/images/btcpng.gif" alt="" class="avatar-photo">
                         </div>
                         <h5 class="text-center h5 mb-0"><?php echo $_SESSION['fullname'];?></h5>
                         <p class="text-center text-muted font-14">Member of Ace Investments.</p>
                       <div class="profile-info">
                            <h5 class="mb-20 h5 text-blue">Contact Information</h5>
                          <ul>
                            <li>
                                <span>Email Address:</span>
                                <?php echo $_SESSION['email'];?>
                            </li>
                            <li>
                                <span>Preferred Crypto:</span>
                                <?php echo $_SESSION['favcrypto'];?>
                            </li>
                              <li>
                                  <span>Balance:</span>
                                 $<?php echo $_SESSION['balance'];?>
                              </li>
                            <li>
                                <span>Country:</span>
                                <?php echo $_SESSION['country'];?>
                            </li>
                            <li>
                                <span>Wallet Address:</span>
                                <?php echo $_SESSION['userwallet'];?>
                            </li>
                          </ul>
                       </div>
                      <div class="profile-skills">
                        <h5 class="mb-20 h5 text-blue">Investment Plans : <p class="text-success"> <?php
                            if ($_SESSION['plans']===0){
                                echo "No Active Plan.";
                            }
                            elseif ($_SESSION['plans']===1){
                                echo "Regular";
                            }
                            elseif ($_SESSION['plans']===2){
                                echo "Bronze";
                            }
                            elseif ($_SESSION['plans']===3){
                                echo "Sliver";
                            }
                            else{
                                echo "Gold";
                            }
                            ?> </p>
                        </h5>
                        <h6 class="mb-5 font-14">Progress</h6>
                        <div class="progress mb-20" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style= "width: <?php echo $pro;?>%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                      </div>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-15">
                    <div class="card-box height-50-p ">
                        <div class="profile-tab height-50-p">
                            <div class="tab height-50-p">
                                <div class=" height-50-p" id="setting" >
                                    <div class="profile-setting justify-content-center">
                                        <form action="profile.php" method="post" enctype="multipart/form-data" id="profile-form">
                                            <ul class="profile-edit-list row ">
                                                <li class="weight-500 col-md-12">
                                                    <h4 class="text-blue h5 mb-20">Edit Your Personal Setting</h4>
                                                    <div class="form-group col-md-6">
                                                        <label>Full Name :</label>
                                                        <input class="form-control form-control-lg" type="text" name="fname" value="<?php echo $_SESSION['fullname'] ?>">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label>Crypto Wallet :</label>
                                                        <input class="form-control form-control-lg" type="text" name="wallet" value="<?php echo $_SESSION['userwallet'] ?>">
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label>Preferred Crypto :</label>
                                                        <input class="form-control form-control-lg" type="text" name="crypto" value="<?php echo $_SESSION['favcrypto'] ?>">
                                                    </div>
                                                    <div class="form-group col-md-6">

                                                       <input name="edit-btn"  class="btn btn-primary btn-lg btn-block" type="submit" value="Submit" >
                                                    </div>

                                                </li>
                                            </ul>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <?php
     if(isset($_GET['update']))
        {

            $message = "Profile Updated Successfully";
            echo " <script type='text/javascript'>$(function (){
      $('#myModal').modal('show');
 }); 
                         </script> ";


        }
            else{
                $message = "Update Failed";
                echo " <script type='text/javascript'>swal(
                {
                    type: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                }
            )</script> ";

            }
            ?>




     <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered" role="document">
             <div class="modal-content">
                 <div class="modal-body text-center font-18">
                     <div class="mb-30 text-center"><img src="vendors/images/81191554933123.596f89a9597ff.gif" style="width: 100px; height: 100px;"></div>
                     <p class="mb-20"><?php echo $message?> </p>

                 </div>
                 <div class="modal-footer justify-content-center">
                     <button type="button" class="btn btn-primary" data-dismiss="modal">Done</button>
                 </div>
             </div>
         </div>
     </div>
    </div>

