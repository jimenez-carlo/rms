<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
  <div class="row-fluid">
    <div class="span12" id="content">
      <div class="row-fluid">
        <!-- Acess block -->
        <div class="block">
          <div class="navbar navbar-inner block-header">
              <div class="pull-left">Page Access</div>
          </div>
          <div class="block-content collapse in">
            <form class="form-horizontal" method="post">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>PAGE ID</th>
                    <th>NAME</th>
                    <th>ACCESSED BY</th>
                  </tr>
                </thead>
                <tbody>
                <?php if ( count($pages) > 0 ): ?>
                  <?php foreach($pages as $page): ?>
                  <tr>
                    <td><?php echo $page->pid; ?></td>
                    <td><?php echo $page->name; ?></td>
                    <td width="65%">
                       <select multiple class="span11" name="position[]">
                      <?php if ( count($positions) > 0 ): ?>
                        <?php foreach($positions as $position): ?>
                          <option value="<?php echo $page->pid ."-". $position->pid; ?>"
                          <?php
                          foreach($access as $acc) {
                            if($page->pid==$acc->page && $position->pid==$acc->position) echo ' selected';
                          }
                          ?>><?php echo $position->name; ?>
                        </option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                    </select>
                  </td>
                </tr>
                  <?php endforeach; ?>
              <?php endif; ?>
              </tbody>
              </table>
             <input type="submit" class="btn btn-success" value="Save" name="save">
            </form>
          </div>
        </div>

        <!-- Page block -->
        <div class="block">
          <div class="navbar navbar-inner block-header">
              <div class="pull-left">New Page</div>
          </div>
          <div class="block-content collapse in">
            <form class="form-horizontal" method="post">
              <div class="control-group">
                <label class="control-label">Name</label>
                <div class="controls">
                  <input type="text" name="page_name">
                </div>
              </div>
              <div class="form-actions">
                <input type="submit" class="btn btn-success" value="Add" name="add">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>