<?php
  chdir(dirname(__FILE__));
  require_once("../dataAccess/sectionDAO.class.php");

  class Section
  {
    private $id;
    private $checklistId;
    private $title;
    private $position;

    function __construct($conn, $sectionId)
    {
      $result = sectionDAO::getSectionInfos($conn, $sectionId);
      $row = $result->fetch_assoc();

      if ($row) {
        $this->id = $row["id"];
        $this->checklistId = $row["checklist_id"];
        $this->title = $row["title"];
        $this->position = $row["position"];
      } else {
        $this->id = NULL;
      }
    }

    public static function getSectionItems($conn, $sectionId)
    {
      return sectionDAO::getSectionItems($conn, $sectionId);
    }

    public function setId($id)
    {
      $this->id = $id;
    }

    public function setChecklistId($checklistId)
    {
      $this->checklistId = $checklistId;
    }

    public function setTitle($title)
    {
      $this->title = $title;
    }

    public function setPosition($position)
    {
      $this->position = $position;
    }

    public function getId()
    {
      return $this->id;
    }

    public function getChecklistId()
    {
      return $this->checklistId;
    }

    public function getTitle()
    {
      return $this->title;
    }

    public function getPosition()
    {
      return $this->position;
    }
  }
?>