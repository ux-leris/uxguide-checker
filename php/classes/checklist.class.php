<?php
  require_once(__DIR__."/../dataAccess/checklistDAO.class.php");

  class Checklist
  {
    private $id;
    private $title;
    private $description;
    private $authorId;
    private $isPublished;

    function __construct($conn, $checklistId)
    {
      $result = checklistDAO::getChecklistInfos($conn, $checklistId);
      $row = $result->fetch_assoc();

      if ($row) {
        $this->id = $row["id"];
        $this->title = $row["title"];
        $this->description = $row["description"];
        $this->authorId = $row["author_id"];
        $this->isPublished = $row["published"];
      } else {
        $this->id = NULL;
      }
    }

      function loadSectionsOfChecklist($conn, $checklist_id)
      {
          $checklistDAO = new ChecklistDAO;

          return $checklistDAO->select_sectionsOfChecklist($conn, $checklist_id);
      }

      function loadLabelsOfChecklist($conn, $checklist_id)
      {
          $checklistDAO = new ChecklistDAO;

          return $checklistDAO->select_checklistLabels($conn, $checklist_id);
      }

    public function setId($id)
    {
      $this->id = $id;
    }

    public function setTitle($title)
    {
      $this->title = $title;
    }

    public function setDescription($description)
    {
      $this->description = $description;
    }

    public function setAuthorId($author)
    {
      $this->author = $author;
    }

    public function getId()
    {
      return $this->id;
    }

    public function getTitle()
    {
      return $this->title;
    }

    public function getDescription()
    {
      return $this->description;
    }

    public function getAuthorId()
    {
      return $this->authorId;
    }

    public function getIsPublished() {
      return $this->isPublished;
    }

    public function getAuthorName($conn)
    {
        $checklistDAO = new ChecklistDAO;

        $result = $checklistDAO->select_authorName($conn, $this->authorId);
        $row = $result->fetch_assoc();

        return $row["name"];
    }

    public function userHasAccess($conn, $userId) 
    {    
      if($this->authorId != $userId) {
        $hasAccess = checklistDAO::verifyAccess($conn, $userId, $this->id);

        if(!$hasAcces) {
          return false;
        }
      }

      return true;
    }

    public function countChecklistItems($conn) 
    {
      $result = checklistDAO::countChecklistItems($conn, $this->id);
      $row = $result->fetch_assoc();

      return $row['count'];
    }

      public function loadEvaluations($conn) {
          $checklistDAO = new ChecklistDAO;

          $result = $checklistDAO->select_checklistEvaluations($conn, $this->id);

          return $result;
      }

    public function publish($conn) {
      return checklistDAO::publish($conn, $this->id);
    }

      public function getNumberOfAnswersBySections($conn, $checklist_id) {
          $checklistDAO = new ChecklistDAO;

          return $checklistDAO->getNumberOfAnswersBySections($conn, $checklist_id);
      }

      public function getNumberOfAnswersByQuestions($conn, $checklist_id, $section_id) {
          $checklistDAO = new ChecklistDAO;

          return $checklistDAO->getNumberOfAnswersByQuestions($conn, $checklist_id, $section_id);
      }
  }
?>