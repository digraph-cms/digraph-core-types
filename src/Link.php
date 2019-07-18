<?php
/* Digraph Core | https://gitlab.com/byjoby/digraph-core | MIT License */
namespace Digraph\Modules\CoreTypes;

use Digraph\DSO\Noun;
use HtmlObjectStrings\A;

class Link extends Noun
{
    const SLUG_ENABLED = true;

    public function searchIndexed()
    {
        return false;
    }

    public function formMap(string $actions) : array
    {
        $s = $this->factory->cms()->helper('strings');
        $map = parent::formMap($actions);
        $map['digraph_title'] = false;
        $map['digraph_body'] = false;
        $map['link_url'] = [
            'weight' => 400,
            'field' => 'url',
            'label' => $s->string('forms.link.url_label'),
            'class' => 'Formward\\Fields\\Url',
            'required' => true
        ];
        $map['showpage'] = [
            'weight' => 401,
            'field' => 'link.showpage',
            'label' => $s->string('forms.link.showpage'),
            'class' => 'Formward\Fields\Checkbox'
        ];
        return $map;
    }

    public function tag_link($text=null, array $args = [])
    {
        $link = new A();
        $link->attr('href', $this['url']);
        $link->addClass('digraph-link-intercept');
        $link->attr('data-digraph-link', $this->url());
        $link->content = $this->name();
        if ($text) {
            $link->content = $text;
        }
        return $link;
    }
}
