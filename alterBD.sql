-- Pending modifications

-- For published
ALTER TABLE checklist ADD COLUMN published bool DEFAULT false;

-- For drag and drop items
ALTER TABLE checklist_item ADD COLUMN item_order integer NOT NULL;

UPDATE checklist_item JOIN (SELECT id AS sub_id, ROW_NUMBER() OVER (partition by section_id ORDER BY id) as rowNumber FROM checklist_item) y on id=sub_id SET item_order = rowNumber

-- For time elapsed in evaluation (helps the calculation of average time of evalutions in a checklist)
ALTER TABLE evaluation ADD column time_elapsed decimal default 0
DROP TABLE pause;