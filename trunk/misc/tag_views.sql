-- SQL views for searching the application's Tag2All table.
--
-- Current suggestion: Get all IDs from the DB into PHP,
-- get all Tag2Alls from the DB into PHP, then match and filter
-- until you're left with a list of (Model-, Set-, Image- or Video-) IDs.
--
-- Then you can request these items very efficiently.



-- AllIDs
create ALGORITHM = UNDEFINED view `vw_IDs`
as
select M.model_id, NULL as set_id, NULL as image_id, NULL as video_id from `Model` as M
	union
select S.model_id, S.set_id, NULL as image_id, NULL as video_id from `Set` as S
	union
select S.model_id, I.set_id, I.image_id, NULL as video_id from `Image` as I join `Set` as S on S.set_id = I.set_id
	union
select S.model_id, V.set_id, NULL as image_id, V.video_id from `Video` as V join `Set` as S on S.set_id = V.set_id



-- Model
select
	M.model_id,
	M.model_firstname,
	M.model_lastname,
	M.model_birthdate,
	M.model_remarks
from
	`Model` as M
where
	M.mut_deleted = -1	
	and
	M.model_id in (1,2,3,4,5,6,7,8,9)



-- Set
select
	S.set_id,
	S.set_prefix,
	S.set_name,
	S.set_containswhat,

	M.model_id,
	M.model_firstname,
	M.model_lastname
from
	`Set` as S
	join `Model` as M on M.model_id = S.model_id
where
	S.mut_deleted = -1
	and
	S.set_id in (1,2,3,4,5,6,7,8,9)



-- Image
select
	VWI.image_id,
	VWI.image_filename,
	VWI.image_fileextension,
	VWI.image_filesize,
	VWI.image_filechecksum,
	VWI.image_width,
	VWI.image_height,
	
	VWI.set_id,
	VWI.set_prefix,
	VWI.set_name,
	VWI.set_containswhat,

	VWI.model_id,
	VWI.model_firstname,
	VWI.model_lastname
from
	vw_Image as VWI
where
	VWI.mut_deleted = -1	
	and
	VWI.image_id in (1,2,3,4,5,6,7,8,9)



-- Video
select
	VWV.video_id,
	VWV.video_filename,
	VWV.video_fileextension,
	VWV.video_filesize,
	VWV.video_filechecksum,
	
	VWV.set_id,
	VWV.set_prefix,
	VWV.set_name,
	VWV.set_containswhat,

	VWV.model_id,
	VWV.model_firstname,
	VWV.model_lastname
from
	vw_Video as VWV
where
	VWV.mut_deleted = -1	
	and
	VWV.video_id in (1,2,3,4,5,6,7,8,9)

