-- Insert Buildings
INSERT INTO buildings (name, description) VALUES ('Mine', 'Extracts resources from the planet');
INSERT INTO buildings (name, description) VALUES ('Research Lab', 'Allows for research projects to be conducted');

-- Insert Technologies
INSERT INTO technologies (name, description, research_time) VALUES ('Energy Technology', 'Improves energy production', 5);

-- Insert Dependencies
INSERT INTO dependencies (item_id, dependency_id) VALUES (2, 1); -- Research Lab depends on Mine
