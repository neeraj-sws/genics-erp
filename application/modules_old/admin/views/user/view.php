  <!-- Content Wrapper. Contains page content -->
  <div class="">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>View User</h1>
          </div>
          <div class="col-sm-6">
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                
                <h3 class="profile-username text-center"><?php echo $uinfo->full_name; ?></h3>
                <p class="text-muted text-center"><?php echo $uinfo->role; ?></p>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-body">
                <div>
                  <div >
                    <!-- Personal Info -->
                    <h3>Deatils</h3>
                    <table class="table table-borderless">
                      <tbody>
                         <tr>
                          <th>Email</th>
                          <td><?php echo $uinfo->email; ?></td>
                        </tr>
                         <tr>
                          <th>Phone</th>
                          <td><?php echo $uinfo->phone; ?></td>
                        </tr>
                         <tr>
                          <th>Address</th>
                          <td><?php echo $uinfo->role; ?></td>
                        </tr>
                          <tr>
                          <th>Status</th>
                          <td><?php if($uinfo->status==0){ echo 'Inactive';}else{ echo 'Active'; } ?></td>
                        </tr>
                      </tbody>
                    </table>
                    <!-- /.Personal Info-->
                  </div>
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
