<?php
/* Digraph Core | https://gitlab.com/byjoby/digraph-core | MIT License */
namespace Digraph\Modules\CoreTypes;

use Digraph\DSO\Noun;

class Versioned extends Noun
{
    const ROUTING_NOUNS = ['versioned'];
    const VERSION_TYPE = 'version';
    const SLUG_ENABLED = true;
    protected $versions;

    public function childEdgeType($child)
    {
        if ($child instanceof Version) {
            return 'version';
        } else {
            return null;
        }
    }

    public function title($verb = null)
    {
        if (!($version = $this->currentVersion())) {
            return parent::title($verb);
        }
        return $version->title($verb);
    }

    public function body()
    {
        if (!($version = $this->currentVersion())) {
            return '[no version of this page is available]';
        }
        return $version->body();
    }

    public function actions($links)
    {
        $links['version_list'] = '!id/versions';
        return $links;
    }

    protected function sortVersions($versions)
    {
        usort($versions, function ($a, $b) {
            return $b->effectiveDate() - $a->effectiveDate();
        });
        return $versions;
    }

    public function availableVersions()
    {
        if ($this->versions === null) {
            $this->versions = $this->sortVersions(
                $this->factory->cms()
                    ->helper('graph')
                    ->children($this['dso.id'], 'version', 1)
            );
        }
        return $this->versions;
    }

    public function currentVersion()
    {
        foreach ($this->availableVersions() as $v) {
            if ($v->effectiveDate() <= time()) {
                return $v;
            }
        }
        return null;
    }

    public function formMap(string $actions): array
    {
        $map = parent::formMap($actions);
        $map['digraph_title'] = false;
        $map['digraph_body'] = false;
        return $map;
    }
}
