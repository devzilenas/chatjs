<?
class ObjSetHtml {

	public static function makeListHeader($list, $url) {
		$links = self::getListLinks($list);
		$out = array();
		$lprev = '<img src="media/img/left8.png" '.HtmlBlock::altTitle(t('Previous')).' />'.t('Previous');
		$lnext = t('Next').'<img src="media/img/right8.png" '.HtmlBlock::altTitle(t('Next')).' />';
		$out[] = '' !== $links[0] ? '<a href="'.$url.'&'.$links[0].'">'.$lprev.'</a>' : $lprev;
		$out[] = '' !== $links[1] ? '<a href="'.$url.'&'.$links[1].'">'.$lnext.'</a>' : $lnext;
		return "<p>".join('&nbsp;',$out).'</p>';
	}

	private static function getListLinks($list) {
		$prev_url = $list->hasPrev() ? 'page='.$list->prevI() : '';
		$next_url = $list->hasNext() ? 'page='.$list->nextI() : '';
		return array($prev_url, $next_url);
	}

}

