
-- Setting a permission group

UPDATE `query` SET `details` = 'UPDATE user SET permission_group_id=(SELECT id FROM permission_group WHERE notes=''_PERMISSION_GROUP_'' LIMIT 1), last_updated_by=''_UPDATED_BY_'', last_updated=NOW() WHERE id=''_USER_ID_''' WHERE `query`.`id` =55;

