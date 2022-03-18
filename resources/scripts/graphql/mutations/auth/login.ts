import gql from 'graphql-tag';

export default gql`
    mutation tryLogin ($formData: any) {
        tryLogin (formData: $formData)
        @rest(path: "auth/login", method: "POST", bodyKey: "formData"){
            NoResponse
        }
    }
`;
