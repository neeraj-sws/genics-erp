   
          <?php 
        if(count($party)  > 0){?>
            <div id="dataparty">
        <ul class="nav_bar">

        <?php 
        foreach($party as $parties){?>
           <li class="lists" data-number="<?php echo $parties->phone;?>" data-name="<?php echo strtoupper($parties->full_name);?>" onclick="part_click(this)"><?php  echo strtoupper($parties->full_name);?> </li> 
            <?php }?>
                
        </ul>
        </div>
   <?php }?>


<!-- 
                        '<div class="form-group mb-3">
            <label for="phone">Phone Number :</label>
            <input type="text" name="phone" id="phone" class="form-control allow_numeric" value="'.@$party_name->phone.'">
        </div>'; -->