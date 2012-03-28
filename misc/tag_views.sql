-- SQL views for searching the application's Tag2All table.
--
--


SELECT
	T2A.tag_id, T2A.model_id, T2A.set_id, T2A.image_id, T2A.video_id
FROM
	`Tag2All` as T2A
WHERE
	tag_id in (160)



SELECT
	T2A.tag_id, S.model_id, S.set_id, null as image_id, null as video_id
FROM
	`Set` as S
	right join `Tag2All` as T2A on (T2A.set_id = S.set_id or T2A.model_id = S.model_id)
WHERE
	tag_id in (160)



SELECT
	T2A.tag_id, S.model_id, S.set_id, IM.image_id, null as video_id
FROM 
	Image as IM
	right join `Set` as S on S.set_id = IM.set_id
	right join `Tag2All` as T2A on ( T2A.image_id = IM.image_id or T2A.set_id = S.set_id or T2A.model_id = S.model_id)
WHERE
	tag_id in (160)




SELECT
	T2A.tag_id, S.model_id, S.set_id, null as image_id, VD.video_id
FROM 
	Video as VD
	right join `Set` as S on S.set_id = VD.set_id
	right join `Tag2All` as T2A on ( T2A.video_id = VD.video_id or T2A.set_id = S.set_id or T2A.model_id = S.model_id)
WHERE
	tag_id in (160)






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

