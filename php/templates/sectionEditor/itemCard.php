<div class="d-flex" item-position="<?= $sectionRow["item_order"] ?>">
  <div id="item-<?= $sectionRow["id"] ?>" class="card col-md-<?= $checklist->getIsPublished() ? "12" : "10" ?> mt-2 mb-2">
    <div class="card-body text-justify d-flex align-items-center">
      <span>
        <i class="fas fa-grip-lines mr-3"></i>
      </span>

      <?php if(!$sectionRow["link"]) { ?>
        <?= $sectionRow["text"] ?>
      <?php } else { ?>
        <a href="<?= $sectionRow["link"] ?>" class="link" target="_blank"><?= $sectionRow["text"] ?></a>
      <?php } ?>

    </div>
  </div>

  <?php if(!$checklist->getIsPublished()) { ?>
    <div id="btnGroup-<?= $sectionRow["id"] ?>" class="col-md-2 d-flex justify-content-evenly align-items-center mt-2 mb-2">
      <button type="button" id="editBtn-<?= $sectionRow["id"] ?>" class="btn btn-secondary mr-1" data-toggle="modal" data-target="#editItem" onClick="loadItemInfos(filterId(this.id))">
        <span>
            <i class="fas fa-edit"></i>
        </span>
        Edit
      </button>
      <button type="button" id="deleteBtn-<?= $sectionRow["id"] ?>" class="btn btn-danger ml-1" data-toggle="modal" data-target="#deleteItem" onClick="loadItemId(filterId(this.id))">
        <span>
            <i class="fas fa-trash-alt"></i>
        </span>
        Delete
      </button>
    </div>
  <?php } ?>

</div>