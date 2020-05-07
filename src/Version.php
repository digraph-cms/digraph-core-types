<?php
/* Digraph Core | https://gitlab.com/byjoby/digraph-core | MIT License */
namespace Digraph\Modules\CoreTypes;

class Version extends Page
{
    const ROUTING_NOUNS = ['version'];
    const UNDIFFABLE_TAGS = [
        'table' => 'table',
        'img' => 'image',
    ];

    public function searchIndexed()
    {
        return false;
    }

    public function parentUrl($verb = 'display')
    {
        if ($verb == 'display') {
            if (($parent = $this->parent()) instanceof Versioned) {
                return $parent->url('versions');
            }
        }
        return parent::parentUrl($verb);
    }

    public function effectiveDate()
    {
        return $this['version.effective_date'] ?? $this['dso.created.date'];
    }

    public function formMap(string $action): array
    {
        $s = $this->factory->cms()->helper('strings');
        $map = parent::formMap($action);
        $map['digraph_name']['default'] = $s->date(time());
        $map['digraph_name']['label'] = $s->string('version.revision_note');
        $map['digraph_title']['required'] = true;
        $map['digraph_title']['label'] = $s->string('version.display_title');
        $map['digraph_slug'] = false;
        $map['version_effectivedate'] = [
            'label' => 'Effective date',
            'class' => 'datetime',
            'weight' => 900,
            'field' => 'version.effective_date',
            'class' => 'datetime',
            'required' => false,
        ];
        if ($action == 'edit') {
            $map['version_effectivedate']['default'] = $this->effectiveDate();
            $map['version_effectivedate']['required'] = true;
        }
        if ($action == 'add') {
            $map['version_effectivedate']['tips'] = [
                'Optionally specify a date for this revision to go live. If this field is left blank this revision will be effective immediately upon being created.',
            ];
            $map['version_effectivedate']['required'] = false;
            if ($parent = $this->cms()->package()->noun()) {
                $map['digraph_title']['default'] = $parent->title();
                if (method_exists($parent, 'currentVersion')) {
                    if ($prev = $parent->currentVersion()) {
                        //confirmation indicating field is prepopulated from previous version
                        $this->factory->cms()->helper('notifications')->confirmation(
                            $s->string('version.confirm_prepopulated')
                        );
                        $map['digraph_title']['default'] = $prev->title();
                        $map['digraph_body']['default'] = $prev['digraph.body'];
                    }
                } else {
                    //error indicating that parent isn't a versioned type
                    $this->factory->cms()->helper('notifications')->warning(
                        $s->string('version.warning_unversionedparent')
                    );
                }
            }
        }
        return $map;
    }
}
