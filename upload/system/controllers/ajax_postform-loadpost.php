<?php
	
	$this->load_langfile('inside/global.php');
	
	echo '<'.'?xml version="1.0" encoding="UTF-8" ?'.'>';
	
	if( !$this->network->id ) {
		echo '<result></result>';
		return;
	}
	if( !$this->user->is_logged ) {
		echo '<result></result>';
		return;
	}
	
	$tmp	= isset($_POST['postid']) ? trim($_POST['postid']) : '';
	if( ! preg_match('/^(public|private)_([0-9]+)$/', $tmp, $m) ) {
		echo '<result></result>';
		return;
	}
	
	$p	= new post($m[1], $m[2]);
	if( $p->error ) {
		echo '<result></result>';
		return;
	}
	if( $p->post_user->id != $this->user->id ) {
		echo '<result></result>';
		return;
	}
	
	$message	= htmlspecialchars(preg_replace('/\s+/ius', ' ', $p->post_message));
	$attached	= array();
	foreach($p->post_attached as $k=>$v) {
		$obj	= (object) array('type'=>$k, 'id'=>$v->attachment_id, 'text'=>'');
		if( $k == 'image' ) {
			$obj->text	= htmlspecialchars(str_cut($v->title, 16));
			$obj->text	= '<a href="'.$C->IMG_URL.'attachments/'.$this->network->id.'/'.$v->file_original.'" target="_blank" onfocus="this.blur();">'.$obj->text.'</a>';
			$obj->text	= htmlspecialchars($obj->text);
		}
		elseif( $k == 'file' ) {
			$obj->text	= htmlspecialchars(str_cut($v->title, 16));
			$obj->text	= '<a href="'.$C->SITE_URL.'getfile/pid:'.$p->post_tmp_id.'/'.htmlspecialchars($v->title).'" target="_blank" onfocus="this.blur();">'.$obj->text.'</a>';
			$obj->text	= htmlspecialchars($obj->text);
		}
		elseif( $k == 'link' ) {
			$obj->text	= htmlspecialchars(str_cut_link($v->link, 16));
			$obj->text	= '<a href="'.$v->link.'" target="_blank" onfocus="this.blur();">'.$obj->text.'</a>';
			$obj->text	= htmlspecialchars($obj->text);
		}
		elseif( $k == 'videoembed' ) {
			$obj->text	= htmlspecialchars(str_cut_link($v->orig_url, 16));
			$obj->text	= '<a href="'.$v->orig_url.'" target="_blank" onfocus="this.blur();">'.$obj->text.'</a>';
			$obj->text	= htmlspecialchars($obj->text);
		}
		$attached[]	= $obj;
	}
	
	echo '<result>';
	echo '<post id="'.$m[1].'_'.$m[2].'" message="'.$message.'">';
	foreach($attached as $obj) {
		echo '<attach type="'.$obj->type.'" id="'.$obj->id.'" text="'.$obj->text.'" />';
	}
	echo '</post>';
	echo '</result>';
	return;
	
?>