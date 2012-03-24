-- SQL views for searching the application's Tag2All table.
--
-- The queries are constructed so that each Model, Set, Image
-- and Video inherits the tags of all its parents AND children.
--



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



-- vw_Tag2AllIDs
create ALGORITHM = UNDEFINED view `vw_Tag2AllIDs`
as
select
	T2A.tag_id, VWI.model_id, VWI.set_id, VWI.image_id, VWI.video_id
from
	Tag2All as T2A
	join vw_IDs as VWI on (VWI.model_id = T2A.model_id or VWI.set_id = T2A.set_id or VWI.image_id = T2A.image_id or VWI.video_id = T2A.video_id)


-- Model
select
	X.tag_id,
	X.model_id,
	M.model_firstname,
	M.model_lastname,
	M.model_birthdate,
	M.model_remarks
from
	`vw_Tag2AllIDs` as X
	join `Model` as M on M.model_id = X.model_id
where
	M.mut_deleted = -1
	and X.tag_id in ( 1, 13 )
group by
	M.model_id


-- Model
select
	VWT.tag_id,
	VWT.tag_name,

	M.model_id,
	M.model_firstname,
	M.model_lastname,
	M.model_birthdate,
	M.model_remarks
from
	vw_Tag2All as VWT
	join vw_IDs as VWI on (VWI.model_id = VWT.model_id or VWI.set_id = VWT.set_id or VWI.image_id = VWT.image_id or VWI.video_id = VWT.video_id)
	join `Model` as M on M.model_id = VWI.model_id
where
	M.mut_deleted = -1	
	and
	VWT.tag_id in (1,2,3,4,5,6,7,8,9)
group by
	M.model_id



-- Set
select
	VWT.tag_id,
	VWT.tag_name,
	
	S.set_id,
	S.set_prefix,
	S.set_name,
	S.set_containswhat,

	M.model_id,
	M.model_firstname,
	M.model_lastname
from
	vw_Tag2All as VWT
	join vw_IDs as VWI on (VWI.model_id = VWT.model_id or VWI.set_id = VWT.set_id or VWI.image_id = VWT.image_id or VWI.video_id = VWT.video_id)
	join `Set` as S on S.set_id = VWI.set_id
	join `Model` as M on M.model_id = S.model_id
where
	S.mut_deleted = -1
	and
	VWT.tag_id in (1,2,3,4,5,6,7,8,9)
group by
	S.set_id



-- Image
select
	VWT.tag_id,
	VWT.tag_name,

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
	vw_Tag2All as VWT
	join vw_Image as VWI on (VWI.image_id = VWT.image_id or VWI.set_id = VWT.set_id or VWI.model_id = VWT.model_id)
where
	VWI.mut_deleted = -1	
	and
	VWT.tag_id in (1,2,3,4,5,6,7,8,9)
group by
	VWI.image_id



-- Video
select
	VWT.tag_id,
	VWT.tag_name,

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
	vw_Tag2All as VWT
	join vw_Video as VWV on (VWV.video_id = VWT.video_id or VWV.set_id = VWT.set_id or VWV.model_id = VWT.model_id)
where
	VWV.mut_deleted = -1	
	and
	VWT.tag_id in (1,2,3,4,5,6,7,8,9)
group by
	VWV.video_id

