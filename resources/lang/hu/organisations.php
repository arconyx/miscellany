<?php

return [
    'create'        => [
        'description'   => 'Új szervezet létrehozása',
        'success'       => 'A(z) :name szervezetet létrehoztuk.',
        'title'         => 'Új szervezet',
    ],
    'destroy'       => [
        'success'   => 'A(z) :name szervezetet töröltük.',
    ],
    'edit'          => [
        'success'   => 'A(z) :name szervezetet frissítettük.',
        'title'     => ':name szervezet szerkesztése',
    ],
    'fields'        => [
        'image'         => 'Kép',
        'location'      => 'Helyszín',
        'members'       => 'Tagok',
        'name'          => 'Név',
        'organisation'  => 'Szülőszervezet',
        'organisations' => 'Alszervezet',
        'relation'      => 'Kapcsolat',
        'type'          => 'Típus',
    ],
    'helpers'       => [
        'descendants'   => 'Ez a lista a szervezet összes leszármazott szervezetét tartalmazza, nem csak a közvetlen alszervezeit.',
        'nested'        => 'Hierarchikus nézetben a szervezeteidet alá-fölérendeltségi viszonyukban tekintheted meg. A legfelső szinten azokat a szervezeteket láthatod, amiknek nincs szülőszervezete, rájuk kattintva pedig megtekintheted alszervezeteiket. Amennyiben az egyes alszervezeteknek saját alszervezeteik vannak, azokra kattintva őket is megtekintheted.',
    ],
    'index'         => [
        'add'           => 'Új szervezet',
        'description'   => ':name szervezeteinek kezelése',
        'header'        => ':name szervezetei',
        'title'         => 'Szervezetek',
    ],
    'members'       => [
        'actions'       => [
            'add'   => 'Tag hozzáadása',
        ],
        'create'        => [
            'description'   => 'Egy tag hozzáadása a szervezethez',
            'success'       => 'A tagot hozzáadtuk a szervezethez',
            'title'         => 'Új tag a(z) :name szervezethez',
        ],
        'destroy'       => [
            'success'   => 'A tagot eltávolítottuk a szervezetből',
        ],
        'edit'          => [
            'success'   => 'A szervezeti tagot frissítettük.',
            'title'     => ':name tagjának módosítása',
        ],
        'fields'        => [
            'character'     => 'Karakter',
            'organisation'  => 'Szervezet',
            'role'          => 'Szerep',
        ],
        'helpers'       => [
            'all_members'       => 'Ez a lista minden karaktert tartalmaz, mely tagja ennek a szervezetnek vagy bármelyik leszármazott szervezetének',
            'direct_members'    => 'A legtöbb szervezetnek tagokra van szüksége a sikeres működéshez. Ez a lista azokat a karaktereket sorolja fel, akik közvetlenül a szervezet tagjai.',
        ],
        'placeholders'  => [
            'character' => 'Válassz ki egy karaktert',
            'role'      => 'Vezető, tag, sárkánylovas, zolg',
        ],
        'title'         => ':name tagjai',
    ],
    'organisations' => [
        'title' => ':name alszervezetei',
    ],
    'placeholders'  => [
        'location'  => 'Válassz ki egy helyszínt!',
        'name'      => 'A szervezet neve',
        'type'      => 'Kultusz, banda, klán, fanklub',
    ],
    'quests'        => [
        'description'   => 'A szervezethez kapcsolódó küldetések',
        'title'         => ':name küldetései',
    ],
    'show'          => [
        'description'   => 'A szervezet részletes nézete',
        'tabs'          => [
            'all_members'   => 'Minden tag',
            'members'       => 'Közvetlen tagok',
            'organisations' => 'Szervezetek',
            'quests'        => 'Küldetések',
            'relations'     => 'Kapcsolatok',
        ],
        'title'         => ':name szervezet',
    ],
];
