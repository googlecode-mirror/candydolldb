-- SQL views for searching the application's Tag2All table.
--
-- The queries are constructed so that each Set inherits its
-- model's tags, each Image and Video its Set's and Model's tags.
--


-- Model
select
	VWT.tag_id,
	VWT.tag_name,

	VWM.model_id,
	VWM.model_firstname,
	VWM.model_lastname,
	VWM.model_birthdate,
	VWM.model_remarks,
	VWM.model_setcount
from
	vw_Tag2All as VWT
	join vw_Model as VWM on VWM.model_id = VWT.model_id
where
	VWM.mut_deleted = -1	
	and
	VWT.tag_id in (1,2,3,4,5,6,7,8,9)
group by
	VWT.model_id



-- Set
select
	VWT.tag_id,
	VWT.tag_name,
	
	VWS.set_id,
	VWS.set_prefix,
	VWS.set_name,
	VWS.set_containswhat,
	VWS.set_amount_pics_in_db,
	VWS.set_amount_vids_in_db,

	VWS.model_id,
	VWS.model_firstname,
	VWS.model_lastname
from
	vw_Tag2All as VWT
	join vw_Set as VWS on (VWS.set_id = VWT.set_id or VWS.model_id = VWT.model_id)
where
	VWS.mut_deleted = -1	
	and
	VWT.tag_id in (1,2,3,4,5,6,7,8,9)
group by
	VWS.set_id



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

