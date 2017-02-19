<?php
/**
 * Created by PhpStorm.
 * User: hodanvl
 * Date: 18.2.17
 * Time: 23:46
 */

namespace App\Presenters;


use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Database\Context;

class PostPresenter extends Presenter
{
    /**
     * @var Context
     */
    private $db;

    /**
     * PostPresenter constructor.
     * @param Context $db
     */
    public function __construct(Context $db)
    {
        $this->db = $db;
    }

    public function renderShow($postId)
    {
        $post = $this->db->table('posts')->get($postId);
        if (!$post)
        {
            $this->error('Stránka nebyla nalezena');
        }
        $this->template->post= $post;
        $this->template->comments= $post->related('comment')->order('date');
    }

    protected function createComponentPostForm()
    {
        $form = new Form;
        $form->addText('user_id', 'Uživatel')->setDefaultValue(1);

        $form->addText('title', 'Titulek:')
            ->setRequired();
        $form->addTextArea('content', 'Obsah:')
            ->setRequired();

        $form->addSubmit('send', 'Uložit a publikovat');
        $form->onSuccess[] = [$this, 'postFormSucceeded'];
        return $form;
    }
    public function postFormSucceeded($form,$values)
    {
        $postId=$this->getParameter('postId');

        if ($postId) {
            $post = $this->database->table('posts')->get($postId);
            $post->update($values);
        } else {
            $post = $this->database->table('posts')->insert($values);
        }

        $this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
        $this->redirect('show', $post->id);
    }
    public function actionEdit($postId)
    {

        $post = $this->db->table('posts')->get($postId);
        if (!$post) {
            $this->error('Příspěvek nebyl nalezen');
        }
        $this['postForm']->setDefaults($post->toArray());
    }
    protected function createComponentCommentForm()
    {
        $form=new Form();
        $form->addText('user')->setDefaultValue(1);
        $form->addTextArea('content','Komentář')->setRequired();
        $form->addSubmit('send','Publikovat komentář');
        $form->onSuccess[]=[$this,'commentFormSucceeded'];
        return $form;


    }
    public function commentFormSucceeded($form,$values)
    {
        $post=$this->getParameter('postId');
        $values=[
            'post'=>$post,
            'user'=>$values->user,
            'content'=>$values->content];
        $this->db->table('comments')->insert($values);
        $this->flashMessage('Děkuji za komentář','success');
        $this->flashMessage('Děkuji za komentář,možná');
        $this->redirect('this');
    }


}