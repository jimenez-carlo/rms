<br><br>
<style>
  li {
    display: inline;
  }

  ul {
    display: block;
  }

  .btn:focus {
    outline: unset;
  }

  .records_per_page_div {
    width: 50%;
    float: left;
  }

  .search_div {
    width: 50%;
    float: right;
    text-align: right;
  }

  .ul_div {
    text-align: right;
  }

  .header_btn {
    width: 100%;
    border: none;
    background: unset;
    text-align: left;
    font-weight: bold;
  }

  .table_container {
    padding: 10px 20px 10px;
  }

  .btn {
    margin-bottom: 10px;
  }

  thead,td,tr,.table td:hover,.table tr:hover {
    outline: 1px solid #ddd !important;
    background: #fff;
  }

  thead {
    background: #ddd;
  }

  tr td:first-child {
    width: 1%;
    white-space: nowrap;
  }
</style>
<div class="table_container">
  <form action="<?php echo BASE_URL; ?>Request/table" method="post">
    <input type="hidden" name="sort" value="<?php echo isset($_POST['sort']) ? $_POST['sort'] : ''; ?>">
    <div class="records_per_page_div">records per page:<select style="width:100px" name="pagination" id="" onchange="this.form.submit()">
        <?php echo isset($_POST['pagination']) ? "<option value={$_POST['pagination']} selected hidden>{$_POST['pagination']}</option>" : ""; ?>
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="20">20</option>
        <option value="50">50</option>
        <option value="0">all</option>
      </select>
    </div>
    <div class="search_div">
      <input type="submit" value="Search" class="btn btn-success"> <input type="text" name='search' value="<?php echo isset($_POST['search']) ? $_POST['search'] : ''; ?>">
    </div>
    <table class="table">
      <thead>
        <tr>
          <td></td>
          <td><button type="submit" name="sort" class="header_btn" value='<?php echo (isset($_POST['sort']) && $_POST['sort'] == 'sid ASC') ? 'sid DESC' : 'sid ASC'; ?>'>ID</button></td>
          <td><button type="submit" name="sort" class="header_btn" value='<?php echo (isset($_POST['sort']) && $_POST['sort'] == 'bname ASC') ? 'bname DESC' : 'bname ASC'; ?>'>Branch</button></td>
          <td><button type="submit" name="sort" class="header_btn" value='<?php echo (isset($_POST['sort']) && $_POST['sort'] == 'registration_type ASC') ? 'registration_type DESC' : 'registration_type ASC'; ?>'>Registration Type</button></td>
          <td><button type="submit" name="sort" class="header_btn" value='<?php echo (isset($_POST['sort']) && $_POST['sort'] == 'payment_method ASC') ? 'payment_method DESC' : 'payment_method ASC'; ?>'>Payment Method</button></td>
          <td><button type="submit" name="sort" class="header_btn" value='<?php echo (isset($_POST['sort']) && $_POST['sort'] == 'si_no ASC') ? 'si_no DESC' : 'si_no ASC'; ?>'>SI NO#</button></td>
        </tr>
      </thead>

      <?php foreach ($table->table as $res) { ?>
        <tr>
          <td><input type="checkbox" name="ids[]" value="<?php echo $res['sid']; ?>" <?php echo (in_array($res['sid'], $_SESSION['ids'])) ? 'checked' : ''; ?>></td>
          <td><?php echo $res['sid']; ?></td>
          <td><?php echo $res['bname']; ?></td>
          <td><?php echo $res['registration_type']; ?></td>
          <td><?php echo $res['payment_method']; ?></td>
          <td><?php echo $res['si_no']; ?></td>
        </tr>
      <?php } ?>
      <?php if ($table->count == 0) { ?>
        <tr>
          <td colspan="5">No Result</td>
        </tr>
      <?php } ?>
    </table>
    <?php if (ceil($table->count / $table->num_results_on_page) > 0) : ?>
      <ul class="ul_div">
        <!-- <li></li> -->
        <?php if ($table->page > 1) : ?>
          <li><button class="btn btn-success" type="submit" name="page" value='<?php echo $table->page - 1; ?>'>Prev</button></li>
        <?php endif; ?>

        <?php if ($table->page > 3) : ?>
          <li><button class="btn btn-success" type="submit" name="page" value='<?php echo 1; ?>'>1</button></li>
          <li><button class="btn btn-success" type="button">...</button></li>
        <?php endif; ?>

        <?php if ($table->page - 2 > 0) : ?>
          <li><button class="btn btn-success" type="submit" name="page" value='<?php echo $table->page - 2; ?>'><?php echo $table->page - 2; ?></button></li>
        <?php endif; ?>
        <?php if ($table->page - 1 > 0) : ?>
          <li><button class="btn btn-success" type="submit" name="page" value='<?php echo $table->page - 1; ?>'><?php echo $table->page - 1; ?></button></li>
        <?php endif; ?>
        <li><button class="btn btn-success" type="submit" name="page" value='<?php echo $table->page; ?>'><?php echo $table->page; ?></button></li>

        <?php if ($table->page + 1 < ceil($table->count / $table->num_results_on_page) + 1) : ?>
          <li><button class="btn btn-success" type="submit" name="page" value='<?php echo $table->page + 1; ?>'><?php echo $table->page + 1; ?></button></li>
        <?php endif; ?>
        <?php if ($table->page + 2 < ceil($table->count / $table->num_results_on_page) + 1) : ?>
          <li><button class="btn btn-success" type="submit" name="page" value='<?php echo $table->page + 2; ?>'><?php echo $table->page + 2; ?></button></li>
        <?php endif; ?>
        <?php if ($table->page < ceil($table->count / $table->num_results_on_page) - 2) : ?>
          <li><button class="btn btn-success" type="button">...</button></li>
          <li><button class="btn btn-success" type="submit" name="page" value='<?php echo ceil($table->count / $table->num_results_on_page) ?>'><?php echo ceil($table->count / $table->num_results_on_page) ?></button></li>
        <?php endif; ?>

        <?php if ($table->page < ceil($table->count / $table->num_results_on_page)) : ?>
          <li> <button class="btn btn-success" type="submit" name="page" value='<?php echo $table->page + 1; ?>'>Next</button></li>
        <?php endif; ?>
      </ul>
    <?php endif; ?>
    <input type="hidden" name="action" value='test'>
    <input type="hidden" name="is_init" value='1'>
  </form>
</div>