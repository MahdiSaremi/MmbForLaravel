<?php

namespace App\Mmb\Sections;

class HomeSection
{

    public function main()
    {
        $this->newResponse("وارد منوی اصلی شدید!")
            ->menu('menu')
            ->send();
    }

    public function menu(Menu $menu)
    {
        return $menu
            ->key([
                [
                    $menu->key("سلام", fn() => $this->response("علیک سلام")),
                    $menu->key("درباره ما", 'about'),
                    $menu->key("خدافظ", 'bye'),
                ],
                [
                    $menu->key("پنل مدیریت", 'panel')->hidden(),
                ]
            ])
            ->schema(fn(Schema $schema) => [
                $schema->commandTo('/hi', "سلام"),
                $schema->command('/bye', "bye"),
            ])
            ->on('bye', fn() => $this->response("خدافظ"));

        // return $menu
        //     ->store()
        //     ->key(fn() => [
        //         [ $menu->key("Random : {$this->random}", 'test') ],
        //     ])
        //     ->with('random');
    }

    public function about()
    {
        $this->response("درباره ما...");
    }

    #[RequireAccess('access_panel')]
    public function panel()
    {
        $this->invoke(PanelSection::class);
    }

    #[RequireAccessModel('video', 'can_edit')]
    public function edit(Video $video)
    {
        
    }


    public function initVideo($id)
    {
        return Video::find($id);
    }

}