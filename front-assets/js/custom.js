function addRow(e){
    var row = '<div class="row"><div class="col-12"><div class="text-end mb-3 pt-2"><a href="javascript:void(0);" onclick="removeRow(this)" class="plusicon"><img src="images/minus.png" alt="plus"></a></div></div><div class="col-12"><div class="form-group mb-3"><label for="itemname">item name :</label>        <input type="text" name="" id="itemname" class="form-control"></div></div><div class="col-6"><div class="form-group mb-3"><label for="quantity">quantity :</label><input type="text" name="" id="quantity" class="form-control"></div></div><div class="col-6"><div class="form-group mb-3"><label for="price">price :</label><input type="text" name="" id="price" class="form-control"></div></div></div>';

    $('.Addrowdata').append(row);

}
function removeRow(e){
    $(e).parent().parent().parent().remove();
}