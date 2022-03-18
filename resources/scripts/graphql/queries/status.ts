import gql from 'graphql-tag';

export default gql`
    query StatusQuery {
        status @rest(path: "status") {
            cattr
            version
            installed
        }
    }
`;
