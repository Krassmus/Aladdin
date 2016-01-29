<?php

/**
 * Brainstorm.php
 * model class for table Brainstorm
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Florian Bieringer <florian.bieringer@uni-passau.de>
 * @copyright   2014 Stud.IP Core-Group
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       3.0
 */
class Brainstorm extends SimpleORMap {

    protected static function configure($config = array()) {
        $config['db_table'] = 'brainstorms';
        $config['has_many']['children'] = array(
            'class_name' => 'Brainstorm',
            'assoc_foreign_key' => 'range_id'
        );
        $config['has_many']['votes'] = array(
            'class_name' => 'BrainstormVote'
        );
        $config['belongs_to']['parent'] = array(
            'class_name' => 'Brainstorm',
            'foreign_key' => 'range_id'
        );
        $config['additional_fields']['power'] = true;
        $config['additional_fields']['myvote'] = true;
        $config['additional_fields']['typename'] = true;
        parent::configure($config);
    }

    public static function getTypes() {
        return array(
            'simple' => _('Standard'),
            'sub' => _('Untergliedert'),
            'commented' => _('Kommentiert')
        );
    }

    public function getBestSubbrainstorm()
    {
        $best = null;
        foreach ($this->children as $subbrainstorm) {
            if (!$best || ($subbrainstorm->power > $best->power)) {
                $best = $subbrainstorm;
            }
        }
        return $best;
    }

    public function getPower() {
        return (int) DBManager::get()->fetchColumn('SELECT SUM(vote) FROM brainstorm_votes WHERE brainstorm_id = ?', array($this->id));
    }

    public function getMyvote() {
        return new BrainstormVote(array($this->id, $GLOBALS['user']->id));
    }

    public function answer($text) {
        return self::create(array(
                    'range_id' => $this->id,
                    'text' => $text
        ));
    }

    public function vote($value, $user_id = null) {
        $user_id = $user_id ? : $GLOBALS['user']->id;
        $vote = new BrainstormVote(array($this->id, $user_id));
        $vote->vote = $value;
        $vote->store();
    }

}
