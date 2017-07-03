<?php
namespace PhpSolution\UserAdminBundle\Controller;

use PhpSolution\UserAdminBundle\Form\Type\ForgotPasswordType;
use PhpSolution\UserAdminBundle\Form\Type\ResetPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ResettingController
 */
class ResettingController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function forgotPasswordAction(Request $request): Response
    {
        $form = $this->createForm(ForgotPasswordType::class, null, ['action' => $this->generateUrl('admin_forgot_password_request')]);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            try {
                $this->get('user_admin.reset_process')->sendLinkForResetPassword($form->getData()['email']);

                return $this->redirectToRoute('admin_forgot_password_request_sent');
            } catch (\Exception $e) {
                return $this->redirectToRoute('admin_forgot_password_request_error');
            }
        }

        return $this->render('UserAdminBundle:Resetting:forgot_password.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param string  $token
     *
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function resetPasswordAction(Request $request, $token): Response
    {
        $resetProcess = $this->get('user_admin.reset_process');
        $user = $resetProcess->getUserByToken($token);
        $form = $this->createForm(ResetPasswordType::class, $user);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $resetProcess->resetPassword($user);

            return $this->redirectToRoute('admin_reset_password_success');
        }

        return $this->render(
            'UserAdminBundle:Resetting:reset_password.html.twig',
            ['email' => $user->getEmail(), 'form' => $form->createView()]
        );
    }
}