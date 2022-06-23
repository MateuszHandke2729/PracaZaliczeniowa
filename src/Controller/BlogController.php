<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use App\Entity\BlogArticles;
use App\Entity\BlogCategories;
use App\Entity\BlogPolls;
use App\Entity\User;
use App\Form\BlogArticleFormType;
use App\Form\BlogCategoryFormType;
use App\Form\BlogPollsType;
use App\Form\BlogPollsVoteType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BlogController extends  AbstractController
{

    /**
     * @throws \Exception
     */
    public function artView(Request $request, ManagerRegistry $doctrine, int $id){
        $article = $doctrine->getRepository(BlogArticles::class)->find($id);

        return $this->render('blog/artView.html.twig', [
            "article" => $article,
        ]);
    }
    public function view(ManagerRegistry $doctrine, int $id): Response
    {
        if ($id != 0) {
            $category = $doctrine->getRepository(BlogCategories::class)->find($id);
            $articles = $category->getArticle();
        } else {
            $articles = $doctrine->getRepository(BlogArticles::class)->findAll();
        }
        return $this->render('blog/view.html.twig', [
            "articles" => $articles,
        ]);
    }
    public function index(): Response
    {
        $name = "Homepage";
        return $this->render('blog/index.html.twig',[
            "name"=>$name,
        ]);
    }
    public function about(): Response
    {
        $name = "About";
        return $this->render('blog/about.html.twig',[
            "name"=>$name,
        ]);
    }
    public function allCategories(ManagerRegistry $doctrine): Response
    {
        $allCategories = $doctrine->getManager()->getRepository(BlogCategories::class)->findAll();
        return $this->render('blog/list.html.twig',[
            "allCategories"=>$allCategories,
        ]);
    }
    public function addCategory(ValidatorInterface $validator,ManagerRegistry $doctrine,Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $category = new BlogCategories();

        $form = $this->createForm(BlogCategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $errors = $validator->validate($form);
            if ($form->isValid()&&count($errors)==0) {
                $this->addFlash(
                    'notice',
                    'You created your category congratz!'
                );
                $em = $doctrine->getManager();
                $em->persist($category);
                $em->flush();
                return $this->redirectToRoute('allCat');
            } else {
                $this->addFlash(
                    'error',
                    'Something went wrong sorry!'
                );
            }
        }
        return $this->render('blog/addCategory.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    public function newpoll(ValidatorInterface $validator,ManagerRegistry $doctrine,Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $polls = $doctrine->getRepository(BlogPolls::class)->findAll();
        $poll = new BlogPolls();
        $form = $this->createForm(BlogPollsType::class, $poll);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $errors = $validator->validate($form);
            if ($form->isValid()&&count($errors)==0) {
                $this->addFlash(
                    'notice',
                    'You created your poll congratz!'
                );
                $poll->setAnswers([$form->get('answer1')->getData(),$form->get('answer2')->getData(),$form->get('answer3')->getData()]);
                $poll->setStatus(true);
                $em = $doctrine->getManager();
                $em->persist($poll);
                $em->flush();
                return $this->redirectToRoute('HomePage');
            } else {
                $this->addFlash(
                    'error',
                    'Something went wrong sorry!'
                );
            }
        }
        return $this->render('blog/newpoll.html.twig', [
            'form' => $form->createView(),
            'polls'=> $polls
        ]);
    }
    public function pollstatus(ManagerRegistry $doctrine,Request $request,$pollid)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $entityManager = $doctrine->getManager();
        $poll = $doctrine->getRepository(BlogPolls::class)->find($pollid);
        $status = $poll->getStatus();
        if($status==true){
            $poll->setStatus(false);
        }
        else{
            $poll->setStatus(true);
        }
        $entityManager->flush();
        return $this->redirectToRoute('newpoll');

    }
    public function poll(ManagerRegistry $doctrine,Request $request,$userid): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $doctrine->getRepository(User::class)->find($userid);
        if($user==null){
            $useremail = null;
        }else {
            $useremail = $user->getEmail();
        }
        $polls = $doctrine->getRepository(BlogPolls::class)->findAll();
        do{
            $pollnumber = rand(0,sizeof($polls)-1);
            $poll = $polls[$pollnumber];
        }while ($poll->getStatus()==false);

        $answers = $poll->getAnswers();
        $form = $this->createFormBuilder([])
            ->add('answers', ChoiceType::class, [
                'choices'  => [
                    $answers[0]  => 1,
                    $answers[1]  => 2,
                    $answers[2]  => 3,
                ],
                'expanded'=>true,
                'multiple'=>true,
                'mapped'=>false
            ])
            ->add('vote',SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if(!in_array($useremail,$poll->getUsersVoted())){
                    $poll->addUsersVoted($useremail);
                }
                $em = $doctrine->getManager();
                $em->persist($poll);
                $em->flush();
                return $this->redirectToRoute('HomePage');
            } else {
                $this->addFlash(
                    'error',
                    'Something went wrong sorry!'
                );

            }

        }

        return $this->render('blog/poll.html.twig', [
            'form' => $form->createView(),
            'poll' => $poll,
            'usermail'=>$useremail

        ]);
    }
    public function addArticle(ValidatorInterface $validator,ManagerRegistry $doctrine,Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $article = new BlogArticles();

        $form = $this->createForm(BlogArticleFormType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $errors = $validator->validate($form);
            if ($form->isValid()&&count($errors)==0) {
                $this->addFlash(
                    'notice',
                    'You created your article congratz!'
                );
                $em = $doctrine->getManager();
                $em->persist($article);
                $em->flush();
                return $this->redirectToRoute('allCat');
            } else {
                return $this->render('blog/addArticle.html.twig', [
                    'form' => $form->createView(),
                    'errors' => $errors
                ]);
            }
        }
        return $this->render('blog/addArticle.html.twig', [
            'form' => $form->createView(),

        ]);
    }
}