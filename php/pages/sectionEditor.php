<?php
    require_once("../classes/database.class.php");
    require_once("../classes/checklist.class.php");
    require_once("../classes/section.class.php");

    session_start();

	if(!isset($_SESSION["USER_ID"]))
	{
		header("location: ./login.php");
	}

    $db = new Database;
    $conn = $db->connect();

    $id = $_GET["id"];

    $section = new Section;
    $section->loadSection($conn, $id);

    if(!$section->get_id()) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        exit();
    }

    $checklist = new Checklist;
    $checklist->loadChecklist($conn, $section->get_checklist_id());

    if(!$checklist->get_id() || $checklist->get_author() != $_SESSION["USER_ID"]) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        exit();
    }
?>

<!doctype html>
<html lang="pt-BR">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="../../css/bootstrap/bootstrap.css">
		
		<!-- CSS Local -->
		<link rel="stylesheet" href="../../css/sectionEditor.css">

        <script src="https://kit.fontawesome.com/bc2cf3ace6.js" crossorigin="anonymous"></script>

		<title>Section Editor</title>
	</head>

	<body>
		<!-- Navbar -->
		<?php include('../templates/navbar.php'); ?>

        <div id="edit-modal" class="modal fade" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Item Edit</h5>
                        <button type="button" class="close" data-dismiss="modal">
							<span>&times;</span>
						</button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label>Item Text</label>
                                <input type="text" id="textItemEdit-input" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Item Link</label>
                                <small class="text-muted">Optional field</small>
                                <input type="text" id="linkItemEdit-input" class="form-control">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="updateItemBtn-null" type="button" class="btn btn-primary" onClick="editItem(this.id)">Save changes</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-5 mb-5">
            <div class="mb-3" style="display: flex; align-items: center;">
                <a href="./checklistManager.php?c_id=<?= $checklist->get_id() ?>"><i class="fas fa-chevron-left fa-lg mr-3" style="color:#8FAD88;"></i></a>
                <h1>Section <?= $section->get_position() + 1 ?></h1>
            </div>
            <p class="lead text-muted text-justify"><?= $section->get_title() ?></p>
            <hr>
            <h4 class="mb-4">Checklist Items</h4>
            <div class="row" id="checklist-itens">

                <?php
                    $itemResult = $section->loadSectionItems($conn, $section->get_id());

                    if($itemResult->num_rows == 0)
                    {
                ?>

                <div class="col-md-12 d-flex justify-content-center">
                    <div id="info-items" class="alert alert-info">
                        <strong>Info:</strong> This checklist doesn't have any item.
                    </div>
                </div>

                <?php
                    }
                    else
                    {
                        while($itemRow = $itemResult->fetch_assoc())
                        {
                ?>

                <div id="item-<?= $itemRow["id"] ?>" class="d-flex col-md-12" data-order="<?= $itemRow["item_order"] ?>">
                    <div class="col-md-<?= $checklist->isPublished() ? 12 : 10 ?> card mt-2 mb-2">
                        <div class="card-body text-justify d-flex align-items-center">
                            <i class="fas fa-bars mr-3"></i>

                            <?php
                                if($itemRow["link"] == NULL) {
                            ?>

                            <?= $itemRow["text"] ?>

                            <?php
                                } else {
                            ?>

                            <a href="<?= $itemRow["link"] ?>" class="link" target="_blank"><?= $itemRow["text"] ?></a>

                            <?php
                                }
                            ?>

                        </div>
                    </div>
                    <?php if(!$checklist->isPublished()) { ?>
                        <div id="btnGroup-<?= $itemRow["id"] ?>" class="col-md-2 d-flex justify-content-evenly align-items-center mt-2 mb-2">
                            <button type="button" id="editBtn-<?= $itemRow["id"] ?>" class="btn btn-secondary mr-1" data-toggle="modal" data-target="#edit-modal" onClick="showItemEditModal(this.id)">
                                <span>
                                    <i class="fas fa-edit"></i>
                                </span>
                                Edit
                            </button>
                            <button type="button" id="<?= $itemRow["id"] ?>" class="btn btn-danger ml-1" onClick="deleteItem(this.id)">
                                <span>
                                    <i class="fas fa-trash-alt"></i>
                                </span>
                                Delete
                            </button>
                        </div>
                    <?php } ?>
                </div>

                <?php
                        }
                    }
                ?>

            </div>

            <div class="card p-4 mt-4 mb-4">
                <h4 class="mb-5">New items</h4>
                <form class="col-md-12" method="POST" action="../controllers/insert_items.php?c_id=<?= $section->get_checklist_id().'&s_id='.$section->get_id() ?>">
                    <div class="col-md-12">
                        <div class="col-md-12 d-flex justify-content-center">
                            <div id="info-newItems" class="alert alert-info">
                                <strong>Info:</strong> None new item has been added to this checklist.
                            </div>
                        </div>
                        <div id="item-area" class="row"></div>
                    </div>
                    <div class="col-md-12 d-flex justify-content-center mt-3 mb-5">
                        <button type="button" class="btn btn-danger mr-2" onClick="sectionItemsController(lastNItems - 1)">
                            <span class="ml-1 mr-2">
                                <i class="fas fa-minus-circle"></i>
                            </span>
                            Item
                        </button>
                        <button type="button" class="btn btn-success ml-2" onClick="sectionItemsController(lastNItems + 1)">
                            <span class="ml-1 mr-2">
                                <i class="fas fa-plus-circle"></i>
                            </span>
                            Item
                        </button>
                    </div>
                    <div class="col-md-12 d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">
                            <span class="ml-1 mr-2">
                                <i class="fas fa-save"></i>
                            </span>
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="../../js/jquery-3.5.1.js"></script>
        <script src="../../js/popper-base.js"></script>
        <script src="../../js/bootstrap/bootstrap.js"></script>
                    
        <!-- Core SortableJS -->
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

        <script type="text/javascript">

            function filterId(id)
            {
                return id.substr(id.indexOf("-") + 1);
            }

        </script>

        <script type="text/javascript">

            var el = document.getElementById('checklist-itens');
            var sortable = Sortable.create(el, {
                onEnd: function () {
                    var list = document.getElementById('checklist-itens').children;
                    var size = list.length;
                    for(i=0; i<size; i++) {
                        var item = list.item(i);
                        var id = item.getAttribute("id").replace("item-", "");
                        var order = item.getAttribute("data-order");
                        if(order != i+1) {
                            $.ajax({
                                type: "POST",
                                url: "../controllers/update_item.php",
                                data: {
                                    id: id,
                                    item_order: i+1,
                                },
                                success: function() {
                                    item.setAttribute("data-order", i+1);
                                }
                            });
                        }
                    }
                }
            });

        </script>

        <script type="text/javascript">

            var lastItemInEditionMode = "null";

            function editItem(btn_id)
            {
                var item_id = filterId(btn_id);

                var text = $("#textItemEdit-input").val();

                var link = null;

                if($("#linkItemEdit-input").val() != "")
                {
                    link = $("#linkItemEdit-input").val();
                }

                $.ajax({
                    type: "POST",
                    url: "../controllers/update_item.php",
                    data: {
                        id: item_id,
                        text: text,
                        link: link
                    },
                    success: function(response)
                    {
                        if(response == 1)
                        {
                            alert("Item atualizado.");

                            $("#item-" + item_id).find(".card-body")[0].innerText = text;
                            $("#item-" + item_id).find("a").attr("href", link);

                            $("#edit-modal").modal("hide");

                            document.location.reload(true);     
                        }
                        else
                        {
                            alert("Erro.");
                        }
                    }
                });
            }

            function showItemEditModal(btn_id)
            {
                $("#linkItemEdit-input").val("");
                $("#textItemEdit-input").val("");

                var item_id = filterId(btn_id);

                $("#updateItemBtn-" + lastItemInEditionMode).attr("id", "updateItemBtn-" + item_id);
                lastItemInEditionMode = item_id;

                var text = $("#item-" + item_id).find(".card-body")[0].innerText;
                var link = $("#item-" + item_id).find("a");

                $("#textItemEdit-input").val(text);
                
                if(link.attr("href") == null)
                {
                    $("#linkItemEdit-input").attr("placeholder", "This item doesn't have any link.");
                }
                else
                {
                    $("#linkItemEdit-input").val(link.attr("href"));
                }
            }

            function deleteItem(item_id)
            {
                $.ajax({
                    type: "POST",
                    url: "../controllers/delete_item.php",
                    data: { id: item_id },
                    success: function(response)
                    {
                        if(response == 1)
                        {
                            alert("Item deletado.");

                            $("#item-" + item_id).fadeOut(350, function() {
                                $("#item-" + item_id).remove();
                            });

                            $("#btnGroup-" + item_id).fadeOut(350, function() {
                                $("#btnGroup-" + item_id).remove();
                            });

                            document.location.reload(true);    
                        }
                        else
                        {
                            alert("Erro.");
                        }
                    }
                });
            }

        </script>

        <script type="text/javascript">

            var lastNItems = 0;

            function sectionItemsController(nItems)
            {
                if(nItems > 0)
                {
                    $("#info-newItems").fadeOut(350).hide();
                }
                else
                {
                    $("#info-newItems").fadeIn(350).show();               
                }

                if(nItems > lastNItems)
                {
                    addItem(nItems);
                }
                else
                {
                    if(nItems >= 0)
                    {
                        delItem(nItems);
                    }
                }
            }

            function addItem(nItems)
            {
                for(var i = lastNItems + 1; i <= nItems; i++)
                {
                    var textInput = $("<div>", {
                        "id": "itemText-" + i,
                        "class": "form-group col-md-8",
                    }).hide().fadeIn(350).append($("<label>", {
                        "text": "New Item " + i + " - Text",
                    })).append($("<input>", {
                        "type": "text",
                        "name": "text[]",
                        "class": "form-control",
                        "placeholder": "Recognition rather than recall.",
                    }).attr("required", true));

                    var linkInput = $("<div>", {
                        "id": "itemLink-" + i,
                        "class": "form-group col-md-4",
                    }).hide().fadeIn(350).append($("<label>", {
                        "class": "form-label",
                        "text": "New Item " + i + " - Link",
                    })).append($("<small>", {
                        "class": "text-muted",
                        "text": " " + "Optional field"
                    })).append($("<input>", {
                        "type": "text",
                        "name": "link[]",
                        "class": "form-control",
                        "placeholder": "https://bit.ly/example",
                    }));

                    lastNItems = nItems;

					$("#item-area").append(textInput);
					$("#item-area").append(linkInput);
                }
            }

            function delItem(nItems)
            {
                for(var i = lastNItems; i > nItems; i--)
                {
                    $("#itemText-" + i).fadeOut(350, function() {
                        $(this).remove();
                    });

                    $("#itemLink-" + i).fadeOut(350, function() {
                        $(this).remove();
                    });

                    lastNItems = nItems;
                }
            }

        </script>
	</body>
</html>